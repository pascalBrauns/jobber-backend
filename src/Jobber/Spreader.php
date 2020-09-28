<?php
namespace Jobber;
use Jobber\Event;

class Spreader {
  static function launcher(Event\Emitter $emitter, string $host, int $port) {
    $emitter->on(
        'plan',
        function(array $payload) use($host, $port) {
          Client::create($host, $port, $payload['id']);
          Client::status($host, $port, $payload['id']);
        }
    );

    $emitter->on(
        'launch',
        function(array $payload) use($host, $port) {
          Client::status($host, $port, $payload['id']);
          Client::lifetime($host, $port, $payload['id']);
        }
    );
  }

  static function runtime(Event\Emitter $emitter, string $host, int $port) {
    $emitter->on(
        'progress',
        fn(array $payload) => Client::progress($host, $port, $payload['id'])
    );
    $emitter->on(
        'log',
        fn(array $payload) => Client::log($host, $port, $payload['id'])
    );

    foreach (['suspend', 'resume'] as $event) {
      $emitter->on($event, function(array $payload) use($host, $port) {
        Client::status($host, $port, $payload['id']);
      });
    }
  }

  static function stopper(Event\Emitter $emitter, string $host, int $port) {
    $events = ['cancel', 'error', 'complete'];
    foreach ($events as $event) {
      $emitter->on($event, function(array $payload) use($event, $host, $port) {
        Client::status($host, $port, $payload['id']);
        Client::lifetime($host, $port, $payload['id']);
      });
    }
  }

  static function removed(Event\Emitter $emitter, $host, $port) {
    $emitter->on('removed', function(array $payload) use($host, $port) {
      Client::removed($host, $port, $payload['id']);
    });
  }

  static function emitter(string $host, int $port) {
    $emitter = new Event\Emitter;
    Spreader::launcher($emitter, $host, $port);
    Spreader::runtime($emitter, $host, $port);
    Spreader::stopper($emitter, $host, $port);
    Spreader::removed($emitter, $host, $port);
    return $emitter;
  }

  static function fromEnvironment() {
    $emitters = [];
    foreach (Environment::$addresses as $address) {
      $host = null;
      $port = null;
      if (strpos($address, ':') !== false) {
        $segments = explode(':', $address);
        $host = $segments[0];
        $port = $segments[1];
      }
      else {
        $host = $address;
        $port = 80;
      }
      $emitters[] = Spreader::emitter($host, $port);
    }

    return $emitters;
  }
}
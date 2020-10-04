<?php
namespace Jobber;
use Jobber\Event;

class Spreader {
  static function launcher(Event\Emitter $emitter, string $host, int $port, string $prefix) {
    $emitter->on(
        'plan',
        function(array $payload) use($prefix, $host, $port) {
          Client::create($host, $port, $prefix, $payload['id']);
          Client::status($host, $port, $prefix, $payload['id']);
        }
    );

    $emitter->on(
        'launch',
        function(array $payload) use($host, $port, $prefix) {
          Client::status($host, $port, $prefix, $payload['id']);
          Client::lifetime($host, $port, $prefix, $payload['id']);
        }
    );
  }

  static function runtime(Event\Emitter $emitter, string $host, int $port, string $prefix) {
    $emitter->on(
        'progress',
        fn(array $payload) => Client::progress($host, $port, $prefix, $payload['id'])
    );
    $emitter->on(
        'log',
        fn(array $payload) => Client::log($host, $port, $prefix, $payload['id'])
    );

    foreach (['suspend', 'resume'] as $event) {
      $emitter->on($event, function(array $payload) use($prefix, $host, $port) {
        Client::status($host, $port, $prefix, $payload['id']);
      });
    }
  }

  static function stopper(Event\Emitter $emitter, string $host, int $port, string $prefix) {
    $events = ['cancel', 'error', 'complete'];
    foreach ($events as $event) {
      $emitter->on($event, function(array $payload) use($event, $host, $port, $prefix) {
        Client::status($host, $port, $prefix, $payload['id']);
        Client::lifetime($host, $port, $prefix, $payload['id']);
      });
    }
  }

  static function removed(Event\Emitter $emitter, $host, $port, string $prefix) {
    $emitter->on('removed', function(array $payload) use($host, $port, $prefix) {
      Client::removed($host, $port, $prefix, $payload['id']);
    });
  }

  static function emitter(string $host, int $port, string $prefix) {
    $emitter = new Event\Emitter;
    Spreader::launcher($emitter, $host, $port, $prefix);
    Spreader::runtime($emitter, $host, $port, $prefix);
    Spreader::stopper($emitter, $host, $port, $prefix);
    Spreader::removed($emitter, $host, $port, $prefix);
    return $emitter;
  }

  private static array $separator = [
      'port' => ':',
      'prefix' => '/'
  ];

  private static function position(string $address) {
    return [
        'port' => strpos($address, Spreader::$separator['port']),
        'prefix' => strpos($address, Spreader::$separator['prefix'])
    ];
  }

  static function host(string $address) {
    $position = Spreader::position($address);
    if ($position['port'] !== false) {
      return substr(
          $address,
          0,
          $position['port']
      );
    }
    else if ($position['prefix'] !== false) {
      return substr(
          $address,
          0,
          $position['prefix']
      );
    }
    else {
      return $address;
    }
  }

  static function port(string $address) {
    $position = Spreader::position($address);
    if ($position['port'] !== false) {
      $start = $position['port'] +1;
      if ($position['prefix'] !== false) {
        return substr(
            $address,
            $start,
            $position['prefix'] - $start
        );
      }
      else {
        return substr($address, $start);
      }
    }
    else {
      return 80;
    }
  }

  static function prefix(string $address) {
    $position = Spreader::position($address);
    if ($position['prefix'] !== false) {
      return substr($address, $position['prefix']);
    }
    else {
      return '';
    }
  }

  static function fromEnvironment() {
    $emitters = [];
    foreach (Environment::$addresses as $address) {
      $emitters[] = Spreader::emitter(
          Spreader::host($address),
          Spreader::port($address),
          Spreader::prefix($address)
      );
    }

    return $emitters;
  }
}

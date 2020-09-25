<?php

namespace Jobber\Reactor\Listener;
require __DIR__ . '/Status.php';
require __DIR__ . '/Internal/Internal.php';
require __DIR__ . '/External/External.php';
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Runtime {
  static function progress() {
    Reactor::$inbox->on('progress', function(array $payload) {
      $id = $payload['id'];
      $progress = null;
      if (!is_null($payload['progress'])) {
        $progress = Job\Progress::fromArray($payload['progress']);
      }
      Runtime\Internal::progress($id, $progress);
      Runtime\External::progress($id, $progress);
    });
  }

  static function log() {
    Reactor::$inbox->on('log', function(array $payload) {
      $id = $payload['id'];
      $message = $payload['log'];
      $update = new Job;
      $update->id = $id;
      $update->logs = [$message];
      Storage::$job->set($update);
    });
  }

  static function suspend() {
    Reactor::$inbox->on('suspend', function(array $payload) {
      $id = $payload['id'];
      Runtime\Internal::suspend($id);
      Runtime\Status::suspend($id);
    });
  }

  static function resume() {
    Reactor::$inbox->on('resume', function(array $payload) {
      $id = $payload['id'];
      Runtime\Internal::resume($id);
      Runtime\Status::resume($id);
    });
  }

  static function listen() {
    Runtime::progress();
    Runtime::log();
    Runtime::suspend();
    Runtime::resume();
  }
}
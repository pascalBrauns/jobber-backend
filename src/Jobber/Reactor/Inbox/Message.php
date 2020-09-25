<?php

namespace Jobber\Reactor\Inbox;
use Throwable;
use Jobber\Reactor\Type;

class Message {
  static function ping() {
    $message = new Type\Message;
    $message->subject = 'ping';
    $message->payload = [];
    return $message;
  }

  static function create(Type\Blueprint $blueprint) {
    $message = new Type\Message;
    $message->subject = 'create';
    $message->payload = $blueprint->toArray();
    return $message;
  }

  static function plan(string $id) {
    $message = new Type\Message;
    $message->subject = 'plan';
    $message->payload = ['id' => $id];
    return $message;
  }

  static function launch(string $id) {
    $message = new Type\Message;
    $message->subject = 'launch';
    $message->payload = ['id' => $id];
    return $message;
  }

  static function suspend(string $id) {
    $message = new Type\Message;
    $message->subject = 'suspend';
    $message->payload = ['id' => $id];
    return $message;
  }

  static function cancel(string $id, bool $setStatus = true) {
    $message = new Type\Message;
    $message->subject = 'cancel';
    $message->payload = ['id' => $id, 'setStatus' => $setStatus];
    return $message;
  }

  static function resume(string $id) {
    $message = new Type\Message;
    $message->subject = 'resume';
    $message->payload = ['id' => $id];
    return $message;
  }

  static function complete(string $id) {
    $message = new Type\Message;
    $message->subject = 'complete';
    $message->payload = ['id' => $id];
    return $message;
  }

  static function progress(string $id, Type\Job\Progress $progress = null) {
    $message = new Type\Message;
    $message->subject = 'progress';
    if (is_null($progress)) {
      $message->payload = [
          'id' => $id,
          'progress' => null
      ];
    }
    else {
      $message->payload = [
          'id' => $id,
          'progress' => $progress->toArray()
      ];
    }
    return $message;
  }

  static function log(string $id, string $log) {
    $message = new Type\Message;
    $message->subject = 'log';
    $message->payload = [
        'id' => $id,
        'log' => $log
    ];
    return $message;
  }

  static function error(string $id, Throwable $error) {
    $message = new Type\Message;
    $message->subject = 'error';
    $message->payload = [
        'id' => $id,
        'error' => $error->getMessage()
    ];
    return $message;
  }

  static function end(string $id) {
    $message = new Type\Message;
    $message->subject = 'end';
    $message->payload = ['id' => $id];
    return $message;
  }

  static function removed(string $id) {
    $message = new Type\Message;
    $message->subject = 'removed';
    $message->payload = ['id' => $id];
    return $message;
  }
}
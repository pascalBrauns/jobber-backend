<?php

namespace Jobber\Reactor\Listener;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;

class Stopper {

  static function complete() {
    Reactor::$inbox->on('complete', function(array $payload) {
      $id = $payload['id'];
      Stopper\Status::complete($id);
      Stopper\Lifetime::end($id);
      Stopper\Internal::pid($id);
      $message = Message::end($id);
      Reactor::$inbox->send($message);
    });
  }

  static function cancel() {
    Reactor::$inbox->on('cancel', function(array $payload) {
      $id = $payload['id'];
      $setStatus = $payload['setStatus'] ? true : false;
      Stopper\Internal::cancel($id);
      if ($setStatus) {
        Stopper\Status::cancel($id);
      }
      Stopper\Lifetime::end($id);
      Stopper\Internal::pid($id);
      Stopper\Propagation::cancel($id, $setStatus);
      $message = Message::end($id);
      Reactor::$inbox->send($message);
    });
  }

  static function error() {
    Reactor::$inbox->on('error', function(array $payload) {
      $id = $payload['id'];
      Reactor\Printer::error($id);
      Stopper\Status::error($id);
      Stopper\Lifetime::end($id);
      Stopper\Propagation::error($id);
      Stopper\Internal::pid($id);
      $message = Message::end($id);
      Reactor::$inbox->send($message);
    });
  }

  static function listen() {
    Stopper::error();
    Stopper::cancel();
    Stopper::complete();
  }
}
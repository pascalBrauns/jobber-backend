<?php

namespace Jobber\Reactor\Listener;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Type\Job;

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
      $current = Reactor\Storage::$job->get($id);
      if ($current->status !== Job\Status::CANCELED) {
        Stopper\Internal::cancel($id);
        Stopper\Status::cancel($id);
        Stopper\Lifetime::end($id);
        Stopper\Internal::pid($id);
        Stopper\Propagation::cancel($id);
        $message = Message::end($id);
        Reactor::$inbox->send($message);
      }
    });
  }

  static function error() {
    Reactor::$inbox->on('error', function(array $payload) {
      $id = $payload['id'];
      $current = Reactor\Storage::$job->get($id);
      if ($current->status !== Job\Status::FAILED) {
        Reactor\Printer::error($id);
        Stopper\Status::error($id);
        Stopper\Lifetime::end($id);
        Stopper\Internal::pid($id);
        Stopper\Internal::error($id);
        Stopper\Propagation::error($id);
        $message = Message::end($id);
        Reactor::$inbox->send($message);
      }
    });
  }

  static function listen() {
    Stopper::error();
    Stopper::cancel();
    Stopper::complete();

    Reactor::$inbox->on('end', function(array $payload) {
      print_r("ENDED: ". $payload['id'] . "\n");
    });
  }
}
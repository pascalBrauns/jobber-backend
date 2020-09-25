<?php

namespace Jobber\Reactor\Listener\Runtime\External;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Storage;

class Complex {
  static function progress(string $id) {
    $parent = Storage::$blueprint->parent($id);
    if ($parent) {
      $job = Storage::$job->get($parent->id);
      $message = Message::progress($job->id);
      Reactor::$inbox->send($message);
    }
  }

  static function suspend(string $id) {

  }

  static function resume(string $id) {

  }
}
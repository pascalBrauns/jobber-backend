<?php

namespace Jobber\Reactor\Listener\Stopper\Internal;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Listener\Runtime\Status;
use Jobber\Reactor\Storage;

class Complex {
  private static function hasEnded(string $id) {
    $current = Storage::$job->get($id);
    $status = $current->status;
    return !in_array($status, Status::RUNTIME);
  }

  static function cancel(string $id) {
    $blueprint = Storage::$job->blueprint($id);
    foreach ($blueprint->jobs as $job) {
      if (Complex::hasEnded($job->id)) {
        Reactor::$inbox->send(Message::cancel($job->id));
      }
    }
  }
}
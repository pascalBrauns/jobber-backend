<?php

namespace Jobber\Reactor\Listener\Stopper\Internal;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Listener\Runtime\Status;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Complex {
  private static function isPending(string $id) {
    $current = Storage::$job->get($id);
    $status = $current->status;
    return in_array($status, [Job\Status::PLANNED, ...Status::RUNTIME]);
  }

  static function cancel(string $id) {
    $blueprint = Storage::$job->blueprint($id);
    foreach ($blueprint->jobs as $job) {
      if (Complex::isPending($job->id)) {
        Reactor::$inbox->send(Message::cancel($job->id));
      }
    }
  }

  static function error(string $id) {
    $blueprint = Storage::$job->blueprint($id);
    foreach ($blueprint->jobs as $job) {
      $current = Storage::$job->get($job->id);
      if (Complex::isPending($job->id)) {
        Reactor::$inbox->send(Message::cancel($job->id));
      }
    }
  }
}
<?php

namespace Jobber\Reactor\Listener\Stopper;
use Error;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Listener\Runtime\Status;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Propagation {
  static function isPending(string $id) {
    $current = Storage::$job->get($id);
    $status = $current->status;
    return in_array($status, Status::RUNTIME);
  }

  static function cancel(string $id) {
    $parent = Storage::$blueprint->parent($id);
    if ($parent) {
      Reactor::$inbox->send(Message::cancel($parent->id));
      foreach ($parent->jobs as $job) {
        if (Propagation::isPending($job->id)) {
          Reactor::$inbox->send(Message::cancel($job->id));
        }
      }
    }
  }

  static function error(string $id) {
    $parent = Storage::$blueprint->parent($id);
    if ($parent && Storage::$job->get($parent->id)->status !== Job\Status::FAILED) {
      $error = new Error("Error: child $id has thrown an error");
      Reactor::$inbox->send(Message::error($parent->id, $error));
    }
  }
}
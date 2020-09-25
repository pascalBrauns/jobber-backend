<?php

namespace Jobber\Reactor\Listener\Stopper;
use Error;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Listener\Runtime\Status;
use Jobber\Reactor\Storage;

class Propagation {
  static function isPending(string $id) {
    $current = Storage::$job->get($id);
    $status = $current->status;
    return in_array($status, Status::RUNTIME);
  }

  static function cancel(string $id, bool $setStatus) {
    $parent = Storage::$blueprint->parent($id);
    if ($parent) {
      Reactor::$inbox->send(Message::cancel($parent->id));
      foreach ($parent->jobs as $job) {
        if (Propagation::isPending($job->id)) {
          Reactor::$inbox->send(Message::cancel($job->id, $setStatus));
        }
      }
    }
  }

  static function error(string $id) {
    $parent = Storage::$blueprint->parent($id);
    if ($parent) {
      $error = new Error("Error: child $id has thrown an error");
      Reactor::$inbox->send(Message::error($parent->id, $error));
      foreach ($parent->jobs as $job) {
        if (Propagation::isPending($job->id)) {
          Reactor::$inbox->send(Message::cancel($job->id, false));
        }
      }
    }
  }
}
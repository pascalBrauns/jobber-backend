<?php

namespace Jobber\Reactor\Listener\Stopper;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Listener\Runtime\Status;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Internal {

  static function isInRuntime(string $id) {
    $current = Storage::$job->get($id);
    $status = $current->status;
    return in_array($status, Status::RUNTIME);
  }

  static function isPending(string $id) {
    $current = Storage::$job->get($id);
    $status = $current->status;
    return Internal::isInRuntime($id) || $status === Job\Status::PLANNED;
  }

  static function isTask(string $id) {
    $current = Storage::$job->get($id);
    return $current->type === Job\Type::TASK;
  }

  static function isComplex(string $id) {
    return !Internal::isTask($id);
  }

  static function cancel(string $id) {
    if (Internal::isPending($id)) {
      Reactor\Printer::cancel($id);
      if (Internal::isTask($id) && Internal::isInRuntime($id)) {
        Internal\Task::cancel($id);
      }
      else if (Internal::isComplex($id)) {
        Internal\Complex::cancel($id);
      }
    }
  }

  static function error(string $id) {
    if (!Internal::isTask($id)) {
      $blueprint = Storage::$job->blueprint($id);
      foreach ($blueprint->jobs as $job) {
        if (Internal::isPending($job->id)) {
          Internal\Complex::error($id);
        }
      }
    }
  }

  static function pid(string $id) {
    $update = new Job;
    $update->id = $id;
    $update->pid = 'none';
    Storage::$job->set($update);
  }
}
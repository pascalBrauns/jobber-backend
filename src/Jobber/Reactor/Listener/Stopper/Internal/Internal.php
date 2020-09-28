<?php

namespace Jobber\Reactor\Listener\Stopper;
use Jobber\Reactor;
use Jobber\Reactor\Listener\Runtime\Status;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Internal {
  static function cancel(string $id) {
    $job = Storage::$job->get($id);
    $isTask = $job->type === Job\Type::TASK;
    $hasEnded = !in_array($job->status, Status::RUNTIME);
    Reactor\Printer::cancel($id);
    if (!$hasEnded && $isTask) {
      Internal\Task::cancel($id);
    }
    else if (!$hasEnded) {
      Internal\Complex::cancel($id);
    }
  }

  static function pid(string $id) {
    $update = new Job;
    $update->id = $id;
    $update->pid = 'none';
    Storage::$job->set($update);
  }
}
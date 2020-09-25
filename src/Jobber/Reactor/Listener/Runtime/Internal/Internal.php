<?php

namespace Jobber\Reactor\Listener\Runtime;
require __DIR__ . '/Task.php';
require __DIR__ . '/Complex.php';
use Jobber\Reactor;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Internal {
  static function progress(string $id, Job\Progress $progress = null) {
    $job = Storage::$job->get($id);

    if (Job::isComplex($job) && $progress === null) {
      Internal\Complex::progress($id);
    }
    else if ($progress !== null) {
      $update = new Job;
      $update->id = $id;
      $update->progress = $progress;
      Reactor\Storage::$job->set($update);
    }

    Reactor\Printer::progress(Reactor\Storage::$job->get($id));
  }

  static function suspend(string $id) {
    $job = Storage::$job->get($id);
    $isActive = $job->status === Job\Status::ACTIVE;
    $isTask = $job->type === Job\Type::TASK;
    if ($isActive && $isTask) {
      Internal\Task::suspend($id);
    }
    else if ($isActive) {
      Internal\Complex::suspend($id);
    }
  }

  static function resume(string $id) {
    $job = Storage::$job->get($id);
    $isSuspended = $job->status === Job\Status::SUSPENDED;
    $isTask = $job->type === Job\Type::TASK;
    if ($isSuspended && $isTask) {
      Internal\Task::resume($id);
    }
    else if ($isSuspended) {
      Internal\Complex::resume($id);
    }
  }
}
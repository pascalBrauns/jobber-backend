<?php

namespace Jobber\Reactor\Listener\Runtime;

use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Status {
  const RUNTIME = [
      Job\Status::ACTIVE,
      Job\Status::SUSPENDED
  ];

  private static function set(string $id, string $status) {
    $update = new Job;
    $update->id = $id;
    $update->status = $status;
    Storage::$job->set($update);
  }

  static function suspend(string $id) {
    $job = Storage::$job->get($id);
    $status = $job->status;
    if ($status === Job\Status::ACTIVE) {
      Status::set($id, Job\Status::SUSPENDED);
    }
  }

  static function resume(string $id) {
    $job = Storage::$job->get($id);
    $status = $job->status;
    if ($status === Job\Status::SUSPENDED) {
      Status::set($id, Job\Status::ACTIVE);
    }
  }
}
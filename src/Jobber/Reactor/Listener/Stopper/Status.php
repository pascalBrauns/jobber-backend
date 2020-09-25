<?php

namespace Jobber\Reactor\Listener\Stopper;

use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;
use Jobber\Reactor\Listener\Runtime;

class Status {

  private static function set(string $id, string $status) {
    $update = new Job;
    $update->id = $id;
    $update->status = $status;
    Storage::$job->set($update);
  }

  static function cancel(string $id) {
    $job = Storage::$job->get($id);
    $status = $job->status;
    if (in_array($status, [Job\Status::PLANNED, ...Runtime\Status::RUNTIME])) {
      Status::set($id, Job\Status::CANCELED);
    }
  }

  static function error(string $id) {
    $job = Storage::$job->get($id);
    $status = $job->status;
    if (in_array($status, [Job\Status::PLANNED, ...Runtime\Status::RUNTIME])) {
      Status::set($id, Job\Status::FAILED);
    }
  }

  static function complete(string $id) {
    Status::set($id, Job\Status::COMPLETED);
  }
}
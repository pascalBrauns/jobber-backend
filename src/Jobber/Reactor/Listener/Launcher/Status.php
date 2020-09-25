<?php

namespace Jobber\Reactor\Listener\Launcher;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Status {
  private static function set(string $id, string $status) {
    $update = new Job;
    $update->id = $id;
    $update->status = $status;
    Storage::$job->set($update);
  }

  static function active(string $id) {
    Status::set($id, Job\Status::ACTIVE);
  }

  static function plan(string $id) {
    Status::set($id, Job\Status::PLANNED);
  }
}
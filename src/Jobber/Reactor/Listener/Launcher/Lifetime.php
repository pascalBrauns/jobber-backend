<?php

namespace Jobber\Reactor\Listener\Launcher;
use DateTime;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Lifetime {
  static function start(string $id) {
    $update = new Job;
    $update->id = $id;
    $update->lifetime = new Job\Lifetime;
    $update->lifetime->start = new DateTime;
    Storage::$job->set($update);
  }
}
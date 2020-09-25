<?php

namespace Jobber\Reactor\Listener\Stopper;
use DateTime;
use Jobber\Reactor\Type\Job;
use Jobber\Reactor\Storage;

class Lifetime {
  static function end(string $id) {
    $update = new Job;
    $update->id = $id;
    $update->lifetime = new Job\Lifetime;
    $update->lifetime->end = new DateTime;
    Storage::$job->set($update);
  }
}
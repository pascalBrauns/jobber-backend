<?php

namespace Jobber\Reactor\Listener\Runtime\Internal;

use Jobber\Reactor;
use Jobber\Reactor\Inbox;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Complex {
  static function progress(string $id) {
    $job = Storage::$job->get($id);
    $update = new Job;
    $update->id = $id;
    $update->progress = Job\Progress::fromArray([
        'completed' => $job->progress->completed +1,
        'pending' => $job->progress->pending -1
    ]);
    Reactor\Storage::$job->set($update);
  }

  static function suspend(string $id) {
    $blueprint = Storage::$job->blueprint($id);
    foreach ($blueprint->jobs as $job) {
      Reactor::$inbox->send(Inbox\Message::suspend($job->id));
    }
  }

  static function resume(string $id) {
    $blueprint = Storage::$job->blueprint($id);
    foreach ($blueprint->jobs as $job) {
      Reactor::$inbox->send(Inbox\Message::resume($job->id));
    }
  }
}
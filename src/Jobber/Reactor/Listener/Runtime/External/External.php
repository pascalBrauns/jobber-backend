<?php

namespace Jobber\Reactor\Listener\Runtime;
require __DIR__ . '/Complex.php';
use Jobber\Reactor;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class External {
  static function progress(string $id, Job\Progress $progress = null) {
    if (is_null($progress)) {
      $progress = Storage::$job->get($id)->progress;
    }
    if ($progress->pending === 0) {
      Reactor::$inbox->send(Reactor\Inbox\Message::complete($id));
      External\Complex::progress($id);
      $parent = Storage::$blueprint->parent($id);
      if ($parent && $parent->type === Job\Type::PIPELINE) {
        External::pipeline($parent);
      }
    }
  }

  private static function pipeline(Reactor\Type\Blueprint $parent) {
    $job = Storage::$job->get($parent->id);
    $progress = $job->progress;
    $child = $parent->jobs[$progress->completed +1];
    if ($child) {
      $message = Reactor\Inbox\Message::launch($child->id);
      Reactor::$inbox->send($message);
    }
  }
}
<?php

namespace Jobber\Reactor\Listener\Stopper\Internal;
use Swoole;
use Jobber\Reactor\Storage;

class Task {
  static function cancel(string $id) {
    $job = Storage::$job->get($id);
    Swoole\Process::kill((int) $job->pid, SIGKILL);
  }
}
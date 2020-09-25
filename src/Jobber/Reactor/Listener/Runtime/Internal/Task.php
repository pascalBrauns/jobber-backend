<?php

namespace Jobber\Reactor\Listener\Runtime\Internal;
use Swoole;
use Jobber\Reactor;
use Jobber\Reactor\Storage;

class Task {
  static function suspend(string $id) {
    $job = Storage::$job->get($id);
    Reactor\Printer::suspend($job);
    Swoole\Process::kill((int) $job->pid, SIGSTOP);
  }

  static function resume(string $id) {
    $job = Storage::$job->get($id);
    Reactor\Printer::resume($job);
    Swoole\Process::kill((int) $job->pid, SIGCONT);
  }
}
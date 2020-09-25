<?php

namespace Jobber\Reactor;
use Console;

class Printer {
  static function status(Type\Job $job) {
    $type = $job->type;
    $id = $job->id;
    $status = $job->status;
    $whitespaces = 8 - strlen($type);
    for ($i = 0; $i < $whitespaces; $i++) {
      $type = $type . " ";
    }
    Console::log($type, "[$id]", $status);
  }

  static function log(Type\Job $job, string $text) {
    $type = $job->type;
    $id = $job->id;
    $whitespaces = 8 - strlen($type);
    for ($i = 0; $i < $whitespaces; $i++) {
      $type = $type . " ";
    }
    Console::log($type, "[$id]", $text);
  }

  static function progress(Type\Job $job) {
    $type = $job->type;
    $id = $job->id;
    $completed = $job->progress->completed;
    $pending = $job->progress->pending;
    $total = $completed + $pending;
    $whitespaces = 8 - strlen($type);
    for ($i = 0; $i < $whitespaces; $i++) {
      $type = $type . " ";
    }
    Console::log($type, "[$id]", "($completed/$total)");
  }

  static function suspend(Type\Job $job) {
    $id = $job->id;
    Console::log("Suspending: $id ...");
  }

  static function resume(Type\Job $job) {
    $id = $job->id;
    Console::log("Resuming: $id ...");
  }

  static function cancel(string $id) {
    Console::log("Canceling: $id ...");
  }

  static function error(string $id) {
    Console::log("Failed: $id ...");
  }
}
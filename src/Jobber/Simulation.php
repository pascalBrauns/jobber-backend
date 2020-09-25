<?php

namespace Jobber;
use Jobber\Reactor\Grid\Task;
use Jobber\Reactor\Inbox\Message;
use Error;

class Simulation {
  static function work(Task $task) {
    $steps = 5;//random_int(10, 30);
    $stepSize = 100 / $steps;
    $task->log("Started");
    for ($i = 0; $i < $steps; $i++) {
      $task->log("Sleeping for 1s");
      sleep(1);
      $task->log("Woke up");
      $task->progress($stepSize * ($i + 1));
      $task->log("Progressed");
    }
    $task->log("Ended");
  }

  static function error(Task $task, int $timeout = 2) {
    $job = $task->job;
    $id = $job->id;
    Reactor::$inbox->send(Message::log($job->id, $job->id . ": Started\n"));
    Reactor::$inbox->send(Message::log($job->id, $job->id . ": Sleeping for $timeout\n"));
    sleep($timeout);
    Reactor::$inbox->send(Message::log($job->id, $job->id . ": Throwing simulated error\n"));
    throw new Error("Simulated error in $id");
  }
}
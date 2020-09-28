<?php

namespace Jobber\Reactor;
require __DIR__ . '/Task.php';
use Jobber\Reactor\Grid\Task;
use Throwable;
use Swoole;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Type\Job;

class Grid {
  /**
   * @var callable $work
   */
  static $work;

  static Swoole\Process $process;

  private static function initialize(Job $job) {
    $progress = new Job\Progress;
    $progress->completed = 0;
    $progress->pending = 100;
    $message = Message::progress($job->id, $progress);
    Reactor::$inbox->send($message);
  }

  private static function register(string $id, string $pid) {
    $update = new Type\Job;
    $update->id = $id;
    $update->pid = $pid;
    Storage::$job->set($update);
  }

  static function setup() {
    Grid::$process = new Swoole\Process(function ($process) {
      while (true) {
        $job = Type\Job::fromJSON($process->pop());
        $task = new Swoole\Process(function() use($job) {
          Grid::initialize($job);
          $work = Grid::$work;
          $task = new Task($job->id);
          try {
            $work($task);
          }
          catch(Throwable $error) {
            Reactor::$inbox->send(Message::error($job->id, $error));
            $task->log($error->getMessage() . "\n" . $error->getTraceAsString());
          }

        });
        $task->start();
        Grid::register($job->id, $task->pid);
      }
    });
    Grid::$process->useQueue(6, 2);
    Grid::$process->start();
  }

  static function launch(Type\Job $job) {
    Grid::$process->push($job->toJSON());
  }
}
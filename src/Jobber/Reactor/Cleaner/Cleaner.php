<?php

namespace Jobber\Reactor;
use Jobber\Reactor;
use Jobber\Environment;
use Jobber\Reactor\Inbox\Message;
use Swoole;
use Jobber\Reactor\Type\Job;

class Cleaner {

  static function run() {
    $process = new Swoole\Process(function() {
      while (true) {
        sleep(1);
        $blueprints = Storage::$blueprint->all();
        foreach ($blueprints as $blueprint) {
          $job = Storage::$job->get($blueprint->id);
          $endStatus = [
              Job\Status::COMPLETED,
              Job\Status::CANCELED,
              Job\Status::FAILED
          ];
          $hasEnded = in_array($job->status, $endStatus);
          if ($hasEnded) {
            $keep = Environment::$persistence -$job->lifetime->end->diff(new \DateTime)->s;
            $id = $blueprint->id;
            print_r("Keep $id for $keep seconds\n");
            if ($keep === 0) {
              Storage::remove($blueprint);
              Reactor::$inbox->send(Message::removed($blueprint->id));
            }
          }
        }
      }
    });
    $process->start();
  }

}
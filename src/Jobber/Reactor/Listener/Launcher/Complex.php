<?php

namespace Jobber\Reactor\Listener\Launcher;
use DateTime;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Complex {
  private static function progress(string $id) {
    $blueprint = Storage::$job->blueprint($id);
    $progress = new Job\Progress;
    $progress->completed = 0;
    $progress->pending = count($blueprint->jobs);
    $job = new Job;
    $job->id = $id;
    $job->progress = $progress;
    Reactor::$inbox->send(Message::progress($id, $progress));
  }

  private static function pipeline(string $id) {
    $blueprint = Storage::$job->blueprint($id);
    $first = $blueprint->jobs[0];
    $message = Message::launch($first->id);
    Reactor::$inbox->send($message);
  }

  private static function bridge(string $id) {
    $blueprint = Storage::$job->blueprint($id);
    foreach ($blueprint->jobs as $job) {
      $message = Message::launch($job->id);
      Reactor::$inbox->send($message);
    }
  }

  static function job(string $id) {
    $job = Storage::$job->get($id);
    Complex::progress($id);
    if ($job->type === 'pipeline') {
      Complex::pipeline($id);
    } else if ($job->type === 'bridge') {
      Complex::bridge($id);
    }
  }
}
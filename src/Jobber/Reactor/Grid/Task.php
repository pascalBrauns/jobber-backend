<?php

namespace Jobber\Reactor\Grid;
use Jobber\Reactor;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;

class Task {
  /**
   * @var Job|null
   */
  public ?Job $job;

  public function __construct($id) {
    $this->job = Storage::$job->get($id);
  }

  public function log(string $text) {
    $now = new \DateTime();
    $id = $this->job->id;
    $time = $now->format('D M d Y H:i:s O');
    $message = Message::log($id, "$id at $time: $text\n");
    Reactor::$inbox->send($message);
  }

  public function progress(int $completed) {
    $id = $this->job->id;
    $pending = ceil(100 - $completed);
    $progress = Job\Progress::fromArray([
        'completed' => $completed,
        'pending' => $pending
    ]);
    $message = Message::progress($id, $progress);
    Reactor::$inbox->send($message);
  }
}
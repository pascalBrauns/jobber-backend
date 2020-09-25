<?php

namespace Jobber\Reactor\Type;

use Jobber\Reactor\Type\Job\Lifetime;
use Jobber\Reactor\Type\Job\Progress;

require __DIR__ . '/Progress.php';
require __DIR__ . '/Lifetime.php';
require __DIR__ . '/Status.php';
require __DIR__ . '/Type.php';

class Job {
  public string $id;
  public string $type;
  public ?string $pid;
  public string $status;
  public Job\Progress $progress;
  public Job\Lifetime $lifetime;
  /**
   * @var string[] $logs
   */
  public array $logs;

  public function toArray() {
    return [
        'id' => $this->id,
        'type' => $this->type,
        'pid' => $this->pid,
        'status' => $this->status,
        'progress' => $this->progress->toArray(),
        'lifetime' => $this->lifetime->toArray(),
        'logs' => $this->logs
    ];
  }

  public function toJSON() {
    return json_encode($this->toArray());
  }

  static public function fromArray(array $data) {
    $job = new Job;
    $job->id = $data['id'];
    $job->type = $data['type'];
    $job->pid = $data['pid'];
    $job->status = $data['status'];
    $job->progress = Progress::fromArray($data['progress']);
    $job->lifetime = Lifetime::fromArray($data['lifetime']);
    $job->logs = $data['logs'];
    return $job;
  }

  static public function fromJSON(string $json) {
    return Job::fromArray(json_decode($json, true));
  }

  static function isComplex(Job $job) {
    return in_array($job->type, [
        Job\Type::PIPELINE,
        Job\Type::BRIDGE
    ]);
  }
}
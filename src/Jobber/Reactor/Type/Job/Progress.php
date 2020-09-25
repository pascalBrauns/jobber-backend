<?php

namespace Jobber\Reactor\Type\Job;

class Progress {
  public int $pending;
  public int $completed;

  function toArray() {
    if (isset($this->pending, $this->completed)) {
      return [
          'pending' => $this->pending,
          'completed' => $this->completed
      ];
    }
    else {
      return [];
    }
  }

  static function fromArray(array $data) {
    $progress = new Progress;
    if (isset($data['pending'], $data['completed'])) {
      $progress->pending = $data['pending'];
      $progress->completed = $data['completed'];
    }
    return $progress;
  }

  function toJSON() {
    return json_encode($this->toArray());
  }

  static function fromJSON(string $json) {
    return Progress::fromArray(json_decode($json, true));
  }
}
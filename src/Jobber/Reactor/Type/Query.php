<?php

namespace Jobber\Reactor\Type;

class Query {
  public string $type;
  public string $payload;
  /**
   * @var Query[] $jobs
   */
  public array $jobs;

  function toArray() {
    $data = ['type' => $this->type];
    if ($this->type === 'task') {
      $data['payload'] = $this->payload;
    }
    else {
      $data['jobs'] = [];
      foreach ($this->jobs as $job) {
        $data['jobs'][] = $job->toArray();
      }
    }

    return $data;
  }

  function toJSON() {
    $data = $this->toArray();
    return json_encode($data, true);
  }

  static function fromArray(array $data) {
    $query = new Query;
    $query->type = $data['type'];
    if ($query->type === 'task') {
      $query->payload = $data['payload'];
    }
    else {
      $query->jobs = [];
      foreach ($data['jobs'] as $job) {
        $query->jobs[] = Query::fromArray($job);
      }
    }
    return $query;
  }

  static function fromJSON(string $json) {
    $data = json_decode($json, true);
    return Query::fromArray($data);
  }
}
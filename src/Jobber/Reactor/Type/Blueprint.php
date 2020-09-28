<?php

namespace Jobber\Reactor\Type;

class Blueprint {
  public string $id;
  public string $type;
  public string $payload;
  /**
   * @var Blueprint[] $jobs
   */
  public array $jobs;

  function get(string $id) {
    if ($this->id === $id) {
      return $this;
    }
    else {
      if ($this->type === 'task') {
        return null;
      }
      else {
        foreach ($this->jobs as $job) {
          $match = $job->get($id);
          if ($match !== null) {
            return $match;
          }
        }
        return null;
      }
    }
  }

  function toArray() {
    $data = [
        'id' => $this->id,
        'type' => $this->type,
    ];

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
    return json_encode($this->toArray());
  }

  static function fromQuery(Query $query) {
    $blueprint = new Blueprint;
    $blueprint->id = uniqid();
    $blueprint->type = $query->type;
    if ($query->type === 'task') {
      $blueprint->payload = $query->payload;
    }
    else {
      $blueprint->jobs = [];
      foreach ($query->jobs as $job) {
        $blueprint->jobs[] = Blueprint::fromQuery($job);
      }
    }
    return $blueprint;
  }

  static function fromArray(array $data) {
    $blueprint = new Blueprint;
    $blueprint->id = $data['id'];
    $blueprint->type = $data['type'];
    if ($blueprint->type === 'task') {
      $blueprint->payload = $data['payload'];
    }
    else {
      $blueprint->jobs = [];
      foreach ($data['jobs'] as $job) {
        $blueprint->jobs[] = Blueprint::fromArray($job);
      }
    }
    return $blueprint;
  }

  static function fromJSON(string $json) {
    $data = json_decode($json, true);
    return Blueprint::fromArray($data);
  }

  function toJob() {
    $job = new Job;
    $job->id = $this->id;
    $job->type = $this->type;
    return $job;
  }

  /**
   * @param Job[] $jobs
   * @return Job[]
   */
  function toJobs(array $jobs = []) {
    $job = new Job;
    $job->id = $this->id;
    $job->type = $this->type;
    if ($job->type === 'task') {
      return [
          ...$jobs,
          $job
      ];
    }
    else {
      return array_reduce(
          $this->jobs,
          /**
           * @param Job[] $jobs
           * @param Blueprint $blueprint
           * @return Job[]
           */
          function(array $jobs, Blueprint $blueprint) {
            return [
                ...$jobs,
                ...$blueprint->toJobs()
            ];
          },
          [...$jobs, $job]
      );
    }
  }
}
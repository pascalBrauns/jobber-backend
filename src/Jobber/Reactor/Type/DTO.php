<?php

namespace Jobber\Reactor\Type;
use Jobber\Reactor\Storage;

class DTO {
  static function fromBlueprint(Blueprint $blueprint) {
    $job = Storage::$job->get($blueprint->id);
    $dto = $job->toArray();
    if (Job::isComplex($job)) {
      $dto['jobs'] = [];
      foreach ($blueprint->jobs as $child) {
        $dto['jobs'][] = DTO::fromBlueprint($child);
      }
    }
    else {
      $dto['payload'] = $blueprint->payload;
    }
    return $dto;
  }
}
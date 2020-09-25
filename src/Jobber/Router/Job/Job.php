<?php

namespace Jobber\Router;
require __DIR__ . '/Single.php';
use Jobber\Reactor;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type;
use Jobber\Reactor\Type\DTO;
use Reflex;
use Reflex\Request;
use Reflex\Response;


class Job {
  private static function filter(array $query, Type\Job $job) {
    if (isset($query['type']) && is_array($query['type'])) {
      return in_array($job->type, $query['type']);
    }
    else if (isset($query['type']) && is_string($query['type'])) {
      return $query['type'] === $job->type;
    }
    else {
      return true;
    }
  }

  /**
   * @param string | string[] $box
   * @param string $value
   * @return boolean
   */
  private static function fit($box, $value) {
    if (is_array($box)) {
      return in_array($value, $box);
    }
    else if (is_string($box)) {
      return $box === $value;
    }
    else {
      return true;
    }
  }

  private static function check(array $query, Type\Job $job) {
    $valid = true;
    if (isset($query['type']) && !Job::fit($query['type'], $job->type)) {
      return false;
    }
    if (isset($query['status']) && !Job::fit($query['status'], $job->status)) {
      return false;
    }
    if (isset($query['start']) && isset($job->lifetime->start)) {
      $valid = $valid && (
          (int) $query['start'] <= $job->lifetime->start->getTimestamp()
      );
    }
    if (isset($query['end']) && isset($job->lifetime->end)) {
      $valid = $valid && (
          (int) $query['end'] >= $job->lifetime->end->getTimestamp()
      );
    }

    return $valid;
  }

  static function all() {
    $router = Reflex::router();
    $router->get('/jobs', function(Request $request, Response $response) {
      $jobs = Reactor\Storage::$job->all();
      $dto = [];
      foreach ($jobs as $job) {
        $current = Storage::$job->get($job->id);
        if (Job::check($request->query(), $current)) {
          $id = $job->id;
          $blueprint = Storage::$job->blueprint($id);
          $dto[] = DTO::fromBlueprint($blueprint);
        }
      }
      $response->json($dto);
    });
    return $router;
  }
}
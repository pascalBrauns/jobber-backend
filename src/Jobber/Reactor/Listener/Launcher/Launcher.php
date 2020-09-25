<?php

namespace Jobber\Reactor\Listener;
require __DIR__ . '/Status.php';
require __DIR__ . '/Complex.php';
require __DIR__ . '/Lifetime.php';
use Jobber\Reactor;
use Jobber\Reactor\Grid;
use Jobber\Reactor\Inbox\Message;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Blueprint;
use Jobber\Reactor\Type\Job;

class Launcher {
  private static function create() {
    Reactor::$inbox->on('create', function(array $payload) {
      $blueprint = Blueprint::fromArray($payload);
      Storage::create($blueprint);
      $jobs = $blueprint->toJobs();
      foreach ($jobs as $job) {
        Reactor::$inbox->send(Message::plan($job->id));
      }
      Reactor::$inbox->send(Message::launch($blueprint->id));
    });
  }

  private static function pid(string $id) {
    $update = new Job;
    $update->id = $id;
    $update->pid = 'none';
    Storage::$job->set($update);
  }

  private static function plan() {
    Reactor::$inbox->on('plan', function($payload) {
      $id = $payload['id'];
      Launcher\Status::plan($id);
      Launcher::pid($id);
    });
  }

  private static function launch() {
    Reactor::$inbox->on('launch', function (array $payload) {
      $id = $payload['id'];
      $job = Storage::$job->get($id);
      Launcher\Lifetime::start($id);
      Launcher\Status::active($id);
      if ($job->type === Job\Type::TASK) {
        Grid::launch($job);
      }
      else {
        Launcher\Complex::job($id);
      }
    });
  }

  static function listen() {
    Launcher::create();
    Launcher::plan();
    Launcher::launch();
  }
}
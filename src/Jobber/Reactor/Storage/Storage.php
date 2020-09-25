<?php

namespace Jobber\Reactor;
require __DIR__ . '/Job.php';
require __DIR__ . '/Table/Blueprint.php';
use Swoole;

class Storage {
  static public Storage\Job $job;
  static public Storage\Table\Blueprint $blueprint;
  static Swoole\Table $job_blueprint;

  static function setup() {
    Storage::$job = new Storage\Job;
    Storage::$blueprint = new Storage\Table\Blueprint;
    Storage::$job_blueprint = new Swoole\Table(8192);
    Storage::$job_blueprint->column('blueprint', Swoole\Table::TYPE_STRING, 32);
    Storage::$job_blueprint->column('job', Swoole\Table::TYPE_STRING, 32);
    Storage::$job_blueprint->create();
  }

  static function create(Type\Blueprint $blueprint) {
    Storage::$blueprint->set($blueprint);
    $jobs = $blueprint->toJobs();
    foreach ($jobs as $job) {
      Storage::$job->set($job);
      Storage::$job_blueprint->set($job->id, [
          'job' => $job->id,
          'blueprint' => $blueprint->id
      ]);
    }
  }

  static function remove(Type\Blueprint $blueprint) {
    $jobs = $blueprint->toJobs();
    foreach ($jobs as $job) {
      Storage::$job->remove($job->id);
      Storage::$job_blueprint->del($job->id);
    }
    Storage::$blueprint->remove($blueprint->id);
  }


}
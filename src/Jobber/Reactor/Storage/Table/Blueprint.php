<?php

namespace Jobber\Reactor\Storage\Table;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\Job;
use Swoole;
use Jobber\Reactor\Type;

class Blueprint {
  public Swoole\Table $blueprint;

  public function __construct() {
    $this->blueprint = new Swoole\Table(8192);
    $this->blueprint->column('bid', Swoole\Table::TYPE_STRING, 32);
    $this->blueprint->column('blueprint', Swoole\Table::TYPE_STRING, 8192);
    $this->blueprint->create();
  }

  public function all() {
    $blueprints = [];
    foreach ($this->blueprint as $bid => $value) {
      $blueprints[] = Type\Blueprint::fromJSON(
          $this->blueprint->get($bid, 'blueprint')
      );
    }
    return $blueprints;
  }

  public function get(string $id) {
    if ($this->blueprint->exists($id)) {
      $json = $this->blueprint->get($id, 'blueprint');
      return Type\Blueprint::fromJSON($json);
    }
    else {
      return null;
    }
  }

  public function set(Type\Blueprint $blueprint) {
    $this->blueprint->set($blueprint->id, [
        'blueprint' => $blueprint->toJSON()
    ]);
  }

  public function remove(string $id) {
    if ($this->blueprint->exists($id)) {
      $this->blueprint->del($id);
    }
  }

  static function parent(
      string $id,
      Type\Blueprint $blueprint = null,
      bool $root = true
  ) {
    if ($root) {
      $blueprint = Storage::$blueprint->get(
          Storage::$job_blueprint->get($id, 'blueprint')
      );
    }
    if ($blueprint->id === $id || $blueprint->type === Job\Type::TASK) {
      return null;
    }
    else if ($blueprint && $blueprint->type !== Job\Type::TASK) {
      foreach ($blueprint->jobs as $child) {
        if ($child->id === $id) {
          return $blueprint;
        }
        else if (Job::isComplex($child->toJob())) {
          $parent = Blueprint::parent($id, $child, false);
          if ($parent !== null) {
            return $parent;
          }
        }
      }
    }
    return null;
  }
}
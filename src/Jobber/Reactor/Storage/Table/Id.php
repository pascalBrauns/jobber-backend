<?php

namespace Jobber\Reactor\Storage\Table;
use Jobber\Reactor\Type;
use Swoole;

class Id {
  public Swoole\Table $table;

  public function __construct() {
    $this->table = new Swoole\Table(8192);
    $this->table->column('id', Swoole\Table::TYPE_STRING, 32);
    $this->table->create();
  }

  public function all() {
    $ids = [];
    foreach ($this->table as $row) {
      $ids[] = $row['id'];
    }
    return $ids;
  }

  public function set(Type\Job $view) {
    $this->table->set($view->id, ['id' => $view->id]);
  }

  public function exists(string $id) {
    return $this->table->exists($id);
  }

  public function remove(string $id) {
    if ($this->table->exists($id)) {
      $this->table->del($id);
    }
  }
}
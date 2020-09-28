<?php

namespace Jobber\Reactor\Storage\Table;
use Swoole;
use Jobber\Reactor\Type;

class Pid {
  public Swoole\Table $table;

  public function __construct() {
    $this->table = new Swoole\Table(8192);
    $this->table->column('id', Swoole\Table::TYPE_STRING, 32);
    $this->table->column('pid', Swoole\Table::TYPE_STRING, 16);
    $this->table->create();
  }

  public function get(string $id) {
    if ($this->table->exists($id)) {
      return $this->table->get($id, 'pid');
    }
    else {
      return null;
    }
  }

  public function set(Type\Job $view) {
    if (isset($view->id, $view->pid)) {
      $this->table->set($view->id, ['pid' => $view->pid]);
    }
  }

  public function remove(string $id) {
    if ($this->table->exists($id)) {
      $this->table->del($id);
    }
  }
}
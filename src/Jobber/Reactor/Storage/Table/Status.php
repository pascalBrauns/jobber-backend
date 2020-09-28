<?php

namespace Jobber\Reactor\Storage\Table;
use Jobber\Reactor\Type;
use Swoole;

class Status {
  public Swoole\Table $table;

  public function __construct() {
    $this->table = new Swoole\Table(8192);
    $this->table->column('id', Swoole\Table::TYPE_STRING, 32);
    $this->table->column('status', Swoole\Table::TYPE_STRING, 16);
    $this->table->create();
  }

  public function get(string $id) {
    return $this->table->get($id, 'status');
  }

  public function set(Type\Job $view) {
    if (isset($view->id, $view->status)) {
      $this->table->set($view->id, [
          'status' => $view->status
      ]);
    }
  }

  public function remove(string $id) {
    if ($this->table->exists($id)) {
      $this->table->del($id);
    }
  }
}
<?php

namespace Jobber\Reactor\Storage\Table;
use Jobber\Reactor;
use Swoole;

class Type {
  public Swoole\Table $table;

  public function __construct() {
    $this->table = new Swoole\Table(8192);
    $this->table->column('id', Swoole\Table::TYPE_STRING, 32);
    $this->table->column('type', Swoole\Table::TYPE_STRING, 32);
    $this->table->create();
  }

  public function get(string $id) {
    return $this->table->get($id, 'type');
  }

  public function set(Reactor\Type\Job $view) {
    if (isset($view->id, $view->type)) {
      $this->table->set($view->id, ['type' => $view->type]);
    }
  }

  public function remove(string $id) {
    if ($this->table->exists($id)) {
      $this->table->del($id);
    }
  }
}
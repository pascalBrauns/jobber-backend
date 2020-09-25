<?php

namespace Jobber\Reactor\Storage\Table;

use Jobber\Reactor\Type;
use Swoole;

class Progress {
  public Swoole\Table $table;

  public function __construct() {
    $this->table = new Swoole\Table(8192);
    $this->table->column('id', Swoole\Table::TYPE_STRING, 32);
    $this->table->column('pending', Swoole\Table::TYPE_INT);
    $this->table->column('completed', Swoole\Table::TYPE_INT);
    $this->table->create();
  }

  public function get($id) {
    $progress = new Type\Job\Progress;

    if ($this->table->exists($id)) {
      $row = $this->table->get($id);

      if (isset($row['completed'])) {
        $progress->completed = $row['completed'];
      }

      if (isset($row['pending'])) {
        $progress->pending = $row['pending'];
      }
    }

    return $progress;
  }

  public function set(Type\Job $view) {
    if (isset($view->id, $view->progress)) {
      $progress = $view->progress;
      $row = [];
      if (isset($progress->completed)) {
        $row['completed'] = $progress->completed;
      }
      if (isset($view->progress->pending)) {
        $row['pending'] = $progress->pending;
      }
      $this->table->set($view->id, $row);
    }
  }

  public function remove($id) {
    if ($this->table->exists($id)) {
      $this->table->del($id);
    }
  }
}
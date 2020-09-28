<?php

namespace Jobber\Reactor\Storage\Table;
use DateTime;
use Jobber\Reactor\Type;
use Swoole;

class Lifetime {
  public Swoole\Table $table;

  public function __construct() {
    $this->table = new Swoole\Table(8192);
    $this->table->column('id', Swoole\Table::TYPE_STRING, 32);
    $this->table->column('start', Swoole\Table::TYPE_STRING, 128);
    $this->table->column('end', Swoole\Table::TYPE_STRING, 128);
    $this->table->create();
  }

  public function get(string $id) {
    $lifetime = new Type\Job\Lifetime;

    if ($this->table->exists($id)) {
      $row = $this->table->get($id);
      if (isset($row['start']) && $row['start'] !== '') {
        $lifetime->start = new DateTime($row['start']);
      }

      if (isset($row['end']) && $row['end'] !== '') {
        $lifetime->end = new DateTime($row['end']);
      }
    }
    return $lifetime;
  }

  public function set(Type\Job $view) {
    if (isset($view->id, $view->lifetime)) {
      $lifetime = $view->lifetime;
      $row = [];
      if (isset($lifetime->start)) {
        $start = $lifetime->start;
        $row['start'] = $start->format('D M d Y H:i:s O');
      }
      if (isset($lifetime->end)) {
        $end = $lifetime->end;
        $row['end'] = $end->format('D M d Y H:i:s O');
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
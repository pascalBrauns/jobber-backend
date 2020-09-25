<?php

namespace Jobber\Reactor\Storage\Table;

use Jobber\Reactor\Type;
use Swoole;

class Log {
  public Swoole\Table $table;

  public function __construct() {
    $this->table = new Swoole\Table(8192);
    $this->table->column('id', Swoole\Table::TYPE_STRING, 32);
    $this->table->column('logs', Swoole\Table::TYPE_STRING, 8192);
    $this->table->create();
  }

  /**
   * @param $id
   * @return string[]
   */
  public function get($id) {
    $raw = $this->table->get($id, 'logs');
    return json_decode($raw) ?? [];
  }

  public function set(Type\Job $view) {
    if (isset($view->id, $view->logs)) {
      $id = $view->id;
      $current = $this->get($id);
      $logs = [
          ...$current,
          ...$view->logs,
      ];
      $serialized = json_encode($logs);
      $this->table->set($id, ['logs' => $serialized]);
    }
  }

  public function remove(string $id) {
    foreach ($this->table as $log) {
      if ($log['id'] === $id) {
        $this->table->del($log['lid']);
      }
    }
  }
}
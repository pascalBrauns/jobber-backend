<?php

namespace Event;

class Emitter {
  private array $handlers = [];

  public function on($event, $callback) {
    if (!isset($this->handlers[$event])) {
      $this->handlers[$event] = [];
    }
    array_push(
        $this->handlers[$event],
        $callback
    );
  }

  public function emit($event, ...$payload) {
    if (isset($this->handlers[$event])) {
      foreach($this->handlers[$event] as $handler) {
        if (count($payload)) {
          $handler(...$payload);
        }
        else {
          $handler(null);
        }
      }
    }
    else {
      // echo "WARNING: No handler for event '$event'\n";
    }
  }
}
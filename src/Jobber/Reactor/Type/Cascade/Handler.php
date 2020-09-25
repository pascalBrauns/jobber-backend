<?php

namespace Jobber\Reactor\Type\Cascade;

use Jobber\Reactor\Type;

interface Handle {
  /**
   * @param Type\Blueprint $parent
   * @param string $id
   * @return Type\Job
   */
  public function __invoke(Type\Blueprint $parent, string $id);
}

class Handler {
  public array $handlers = [];

  public function set(string $type, Handle $handle) {
    $this->handlers[$type] = $handle;
  }

  /**
   * @param Type\Blueprint $parent
   * @param string $id
   * @return Handle
   */
  public function handle(Type\Blueprint $parent, string $id) {
    $handle = $this->handlers[$parent->type];
    return $handle($parent, $id);
  }

}
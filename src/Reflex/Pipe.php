<?php

namespace Reflex;

interface Handler {
  public function run($request, $response, ?callable $handle);
}

class Pipe {
  /**
   * @var Handler[] $handlers
   */
  public array $handlers = [];

  public function handle(Request $request, Response $response, $i = 0) {
    if (isset($this->handlers[$i])) {
      $handler = $this->handlers[$i];
      $handler->run(
          $request,
          $response,
          function() use($request, $response, $i) {
            $this->handle($request, $response, $i +1);
          }
      );
    }
  }
}
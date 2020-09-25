<?php

namespace Reflex;

class Middleware {
  /**
   * @var callable
   */
  private $handle;

  public function __construct(callable $handle) {
    $this->handle = $handle;
  }

  public function run(Request $request, Response $response, callable $next) {
    $handle = $this->handle;
    $handle($request, $response, function() use (&$request, $next) {
      $request->handled = false;
      $next();
    });
  }
}
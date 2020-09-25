<?php

namespace Reflex;

class Endpoint {
  private string $pattern;
  /**
   * @var callable
   */
  private $handle;
  private string $method;

  public function __construct(string $method, string $pattern, callable $handle) {
    $this->method = $method;
    $this->pattern = $pattern;
    $this->handle = $handle;
  }

  public function prefix(string $prefix) {
    $pattern = $prefix . $this->pattern;
    $this->pattern = $pattern;
  }

  public function match(Request $request, Response $response) {
    return (
        $request->method() === $this->method &&
        Match::checksum($this->pattern, $request->path()) &&
        Match::segments($this->pattern, $request->path())
    );
  }

  public function run(Request $request, Response $response, callable $next) {
    if ($this->match($request, $response)) {
      $request->pattern = $this->pattern;
      $request->handled = true;
      $handle = $this->handle;
      $handle($request, $response);
    }
    else {
      $next();
    }
  }
}
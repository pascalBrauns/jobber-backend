<?php

namespace Reflex;

class Router extends Middleware {
  public Pipe $pipe;

  public function __construct() {
    $this->pipe = new Pipe;
    parent::__construct(function(Request $request, Response $response, callable $next) {
      $this->pipe->handle($request, $response);
      if ($request->handled === false) {
        $next();
      }
    });
  }

  private function prefix(string $prefix) {
    foreach ($this->pipe->handlers as $handler) {
      if ($handler instanceof Endpoint || $handler instanceof Router) {
        $handler->prefix($prefix);
      }
    }
  }

  private function endpoint(string $method, string $pattern, callable $handle) {
    return new Endpoint(
        $method,
        isset($this->prefix) ? $this->prefix . $pattern : $pattern,
        $handle
    );
  }

  public function get(string $pattern, callable $handle) {
    $this->pipe->handlers[] = $this->endpoint('GET', $pattern, $handle);
  }

  public function post(string $pattern, callable $handle) {
    $this->pipe->handlers[] = $this->endpoint('POST', $pattern, $handle);
  }

  public function put(string $pattern, callable $handle) {
    $this->pipe->handlers[] = $this->endpoint('PUT', $pattern, $handle);
  }

  public function delete(string $pattern, callable $handle) {
    $this->pipe->handlers[] = $this->endpoint('DELETE', $pattern, $handle);
  }

  public function use(...$arguments) {
    $length = count($arguments);

    if ($length === 1 && is_callable($arguments[0])) {
      $handle = $arguments[0];
      $this->pipe->handlers[] = new Middleware($handle);
    }
    else if ($length === 1 && $arguments[0] instanceof Endpoint) {
      $endpoint = $arguments[0];
      $this->pipe->handlers[] = $endpoint;
    }
    else if ($length === 1 && $arguments[0] instanceof Router) {
      $router = $arguments[0];
      $this->pipe->handlers[] = $router;
    }
    else if ($length === 1 && $arguments[0] instanceof Middleware) {
      $middleware = $arguments[0];
      $this->pipe->handlers[] = $middleware;
    }
    else if ($length === 2 && is_string($arguments[0]) && $arguments[1] instanceof Endpoint) {
      $prefix = $arguments[0];
      $endpoint = $arguments[1];
      $endpoint->prefix($prefix);
      $this->pipe->handlers[] = $endpoint;
    }
    else if ($length === 2 && is_string($arguments[0]) && $arguments[1] instanceof Router) {
      $prefix = $arguments[0];
      $router = $arguments[1];
      $router->prefix($prefix);
      $this->pipe->handlers[] = $router;
    }
  }
}


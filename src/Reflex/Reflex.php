<?php
require __DIR__ . '/Request.php';
require __DIR__ . '/Pipe.php';
require __DIR__ . '/Response.php';
require __DIR__ . '/Middleware.php';
require __DIR__ . '/Router.php';
require __DIR__ . '/App.php';
require __DIR__ . '/Endpoint.php';
require __DIR__ . '/Match.php';
require __DIR__ . '/Parameter.php';
require __DIR__ . '/Parser.php';

class Reflex {
  static function app($timeout = 20000) {
    return new Reflex\App($timeout);
  }

  static function router() {
    return new Reflex\Router();
  }

  static function endpoint(string $method, string $pattern, callable $handle) {
    return new Reflex\Endpoint($method, $pattern, $handle);
  }
}
<?php

namespace Jobber\Router;

use Jobber\Reactor\Storage;
use Reflex;

class Blueprint {
  static function all() {
    $router = Reflex::router();
    $router->get(
        '/blueprints',
        function(Reflex\Request $request, Reflex\Response $response) {
          $response->json(Storage::$blueprint->all());
        }
    );
    return $router;
  }
}
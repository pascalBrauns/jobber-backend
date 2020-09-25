<?php

namespace Jobber;
require __DIR__ . '/Job/Job.php';
require __DIR__ . '/Blueprint.php';
use Error;
use Reflex;
use Reflex\Request;
use Reflex\Response;

class Router {
  static function debug() {
    $router = Reflex::router();
    $router->get('/ping', function(Request $request, Response $response) {
      Reactor::$inbox->send(Reactor\Inbox\Message::ping());
      $response->send('pong');
    });

    $router->get('/error', function() {
      throw new Error('Simulated error');
    });

    $router->get('/timeout', fn() => null);
    return $router;
  }

  static function job() {
    $router = Reflex::router();
    $router->use(Router\Job::all());
    $router->use(Router\Job\Single::router());
    return $router;
  }

  static function blueprint() {
    return Router\Blueprint::all();
  }
}

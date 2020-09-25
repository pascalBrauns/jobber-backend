<?php
require __DIR__ . '/Environment.php';
require __DIR__ . '/Reactor/Reactor.php';
require __DIR__ . '/Router/Router.php';
require __DIR__ . '/Simulation.php';
require __DIR__ . '/Client.php';
require __DIR__ . '/Spreader/Spreader.php';

use Jobber\Reactor;

class Jobber {

  /**
   * @param Event\Emitter[] $emitters
   * @return Reactor
   */
  static function reactor(...$emitters) {
    return new Reactor(...$emitters);
  }

  static function router() {
    $router = Reflex::router();
    $router->use('/debug', Jobber\Router::debug());
    $router->use(Jobber\Router::job());
    $router->use(Jobber\Router::blueprint());
    return $router;
  }
}
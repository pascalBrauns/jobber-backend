<?php
use Jobber\Reactor;
use Jobber\Event;

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
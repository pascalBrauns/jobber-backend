<?php

namespace Jobber\Reactor;

class Listener {
  static function listen() {
    Listener\Debug::listen();
    Listener\Launcher::listen();
    Listener\Runtime::listen();
    Listener\Stopper::listen();
  }
}
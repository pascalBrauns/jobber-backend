<?php

namespace Jobber\Reactor;
require __DIR__ . '/Debug.php';
require __DIR__ . '/Launcher/Launcher.php';
require __DIR__ . '/Runtime/Runtime.php';
require __DIR__ . '/Stopper/Stopper.php';

class Listener {
  static function listen() {
    Listener\Debug::listen();
    Listener\Launcher::listen();
    Listener\Runtime::listen();
    Listener\Stopper::listen();
  }
}
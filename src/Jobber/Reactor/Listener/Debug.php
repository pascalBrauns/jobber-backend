<?php

namespace Jobber\Reactor\Listener;
use Jobber\Reactor;

class Debug {
  static function ping() {
    Reactor::$inbox->on('ping', function() {
      echo "Received ping!\n";
    });
  }

  static function listen() {
    Debug::ping();
  }
}
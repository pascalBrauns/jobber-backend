<?php

namespace Jobber;

class Environment {
  /**
   * @var string[] $addresses
   */
  static public array $addresses;
  static public int $port;
  static public int $persistence;

  static function setup() {
    Environment::$addresses = explode(';', $_ENV['ADDRESSES']);
    if (isset($_ENV['PORT'])) {
      Environment::$port = (int) $_ENV['PORT'];
    }
    else {
      Environment::$port = 80;
    }
    if (isset($_ENV['PERSISTENCE'])) {
      Environment::$persistence = (int) $_ENV['PERSISTENCE'];
    }
    else {
      Environment::$persistence = 10 * 60;
    }
  }
}

Environment::setup();
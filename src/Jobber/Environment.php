<?php
namespace Jobber;

class Environment {
  /**
   * @var string[] $addresses
   */
  static public array $addresses;
  static public int $port;
  static public int $persistence;

  static function load(string $path) {
    $env = parse_ini_file($path);
    Environment::$addresses = explode(';', $env['ADDRESSES']);
    if (isset($env['PORT'])) {
      Environment::$port = (int) $env['PORT'];
    }
    else {
      Environment::$port = 80;
    }
    if (isset($env['PERSISTENCE'])) {
      Environment::$persistence = (int) $env['PERSISTENCE'];
    }
    else {
      Environment::$persistence = 10 * 60;
    }
  }
}
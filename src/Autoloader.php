<?php
require __DIR__ . '/../vendor/autoload.php';

class Autoloader {
  /* @var string[] $loaded */
  static array $loaded = [];

  static private function exists(string $path) {
    return file_exists(__DIR__.$path);
  }

  static private function normalize(string $class) {
    $parts = explode('\\', $class);
    return implode('/', $parts);
  }

  static private function resolve(string $class) {
    $normalized = Autoloader::normalize($class);
    if (Autoloader::exists("/$normalized.php")) {
      return __DIR__."/$normalized.php";
    }
    else {
      $parts = explode('\\', $class);
      $name = $parts[count($parts) -1];
      if (Autoloader::exists("/$normalized/$name.php")) {
        return __DIR__."/$normalized/$name.php";
      }
      else {
        print_r("Error: Could not load '$class'.\n");
        return null;
      }
    }
  }

  static private function load(string $class) {
    $path = Autoloader::resolve($class);
    if ($path !== null && !in_array($path, Autoloader::$loaded)) {
      require $path;
      Autoloader::$loaded[] = $path;
    }
  }

  static function register() {
    spl_autoload_register(function($class) {
      Autoloader::load($class);
    });
  }
}

Autoloader::register();
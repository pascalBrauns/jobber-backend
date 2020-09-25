<?php

namespace Jobber\Reactor\Type;

use ReflectionClass;

abstract class Enum {
  static string $name = __CLASS__;
  static function register() {
    return Enum::$name = static::class;
  }

  static function list() {
    $reflection = new ReflectionClass(Enum::$name);
    return $reflection->getConstants();
  }

  static function exists(string $status) {
    return in_array($status, Enum::list());
  }
}
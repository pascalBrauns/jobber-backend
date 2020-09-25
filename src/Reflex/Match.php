<?php

namespace Reflex;

class Match {
  static function checksum(string $pattern, string $path) {
    $segments = [
        'pattern' => explode('/', $pattern),
        'path' => explode('/', $path)
    ];
    return count($segments['pattern']) === count($segments['path']);
  }

  static function segment(string $pattern, string $path) {
    return $pattern[0] === ':' || $pattern === $path;
  }

  static function segments(string $pattern, string $path) {
    $segments = [
        'pattern' => explode('/', $pattern),
        'path' => explode('/', $path)
    ];
    $length = count($segments['pattern']);
    for ($i = 0; $i < $length; $i++) {
      $segment = [
          'pattern' => $segments['pattern'][$i],
          'path' => $segments['path'][$i]
      ];
      if (Match::segment($segment['pattern'], $segment['path'])) {
        continue;
      }
      else {
        return false;
      }
    }
    return true;
  }
}
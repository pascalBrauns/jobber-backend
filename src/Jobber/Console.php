<?php

namespace Jobber;

class Console {
  static function log(...$messages) {
    echo array_reduce(
            $messages,
            function($text, $message) {
              $glue = strlen($text) ? ' ' : '';
              $additional = (
              is_array($message)
                  ? json_encode($message, JSON_PRETTY_PRINT)
                  : $message
              );
              return implode($glue, [$text, $additional]);
            },
            ''
        ) . "\n";
  }
}
<?php

namespace Reflex;

class Parser {
  const Separator = "\r\n";
  private static function segments(string $text) {
    return explode(
        Parser::Separator . Parser::Separator,
        $text
    );
  }

  private static function meta(string $text) {
    return Parser::segments($text)[0];
  }

  private static function head(string $text) {
    $meta = Parser::meta($text);
    $segments = explode(Parser::Separator, $meta);
    $line = $segments[0];
    return explode(' ', $line);
  }

  static function method(string $text) {
    return Parser::head($text)[0];
  }

  static function path(string $text) {
    $segment = Parser::head($text)[1];
    $index = strpos($segment, '?');
    if ($index === false) {
      return $segment;
    }
    else {
      return substr($segment, 0, $index);
    }
  }

  static function query(string $text) {
    $segment = Parser::head($text)[1];
    $index = strpos($segment, '?');
    if ($index === false) {
      return [];
    }
    else {
      $raw = substr($segment, $index +1);
      if (strlen($raw)) {
        $query = [];
        foreach (explode('&', $raw) as $parameter) {
          $index = strpos($parameter, '=');
          $key = substr($parameter, 0, $index);
          $value = substr($parameter, $index +1);
          if (isset($query[$key])) {
            if (is_array($query[$key])) {
              $query[$key][] = $value;
            }
            else {
              $current = $query[$key];
              $query[$key] = [
                  $current,
                  $value
              ];
            }
          }
          else {
            $query[$key] = $value;
          }
        }
        return $query;
      }
      else {
        return [];
      }
    }
  }

  static function version(string $text) {
    return Parser::head($text)[2];
  }

  static function headers(string $text) {
    $meta = Parser::meta($text);
    $raw = array_slice(
        explode(Parser::Separator, $meta),
        1
    );
    $headers = [];
    $separator = ': ';
    foreach ($raw as $header) {
      $index = strpos($header, $separator
      );
      $key = substr($header, 0, $index);
      $value = substr($header, $index +strlen($separator));
      $headers[$key] = $value;
    }
    return $headers;
  }

  static function body(string $text) {
    return Parser::segments($text)[1];
  }
}
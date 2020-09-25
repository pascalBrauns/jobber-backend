<?php

namespace Reflex;

class Request {
  private string $text;
  public string $pattern;
  public bool $handled = false;

  public function __construct(string $text) {
    $this->text = $text;
  }

  public function method() {
    return Parser::method($this->text);
  }

  public function path() {
    return Parser::path($this->text);
  }

  public function query() {
    return Parser::query($this->text);
  }

  public function version() {
    return Parser::version($this->text);
  }

  /**
   * @return Parameter[]|bool
   */
  function parameters() {
    $pattern = $this->pattern;
    $path = $this->path();
    $segments = [
        'pattern' => explode('/', $pattern),
        'path' => explode('/', $path)
    ];
    $parameters = [];
    $length = count($segments['pattern']);
    for ($i = 0; $i < $length; $i++) {
      $segment = [
          'pattern' => $segments['pattern'][$i],
          'path' => $segments['path'][$i]
      ];
      if (Match::segment($segment['pattern'], $segment['path'])) {
        if ($segment['pattern'][0] === ':') {
          $parameter = new Parameter;
          $parameter->name = substr($segment['pattern'], 1);
          $parameter->value = $segment['path'];
          $parameters[] = $parameter;
        }
        continue;
      }
      else {
        return false;
      }
    }
    return $parameters;
  }

  function parameter(string $name) {
    $parameters = $this->parameters();
    foreach ($parameters as $parameter) {
      if ($parameter->name === $name) {
        return $parameter->value;
      }
    }
    return false;
  }

  function headers() {
    return Parser::headers($this->text);
  }

  function body() {
    return Parser::body($this->text);
  }
}
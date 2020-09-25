<?php

namespace Reflex;

class Parameter {
  public string $name;
  public string $value;

  public function __toString() {
    return $this->value;
  }
}
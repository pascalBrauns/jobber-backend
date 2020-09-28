<?php

namespace Jobber\Reactor\Type\Job;
use DateTime;

class Lifetime {
  public DateTime $start;
  public DateTime $end;

  public function toArray() {
    $data = [];
    if (isset($this->start)) {
      $data['start'] = $this->start->format('D M d Y H:i:s O');
    }
    if (isset($this->end)) {
      $data['end'] = $this->end->format('D M d Y H:i:s O');
    }
    return $data;
  }

  public function toJSON() {
    return json_encode($this->toArray());
  }

  static public function fromArray(array $data) {
    $lifetime = new Lifetime;
    if (isset($data['start'])) {
      $lifetime->start = $data['start'];
    }
    if (isset($data['end'])) {
      $lifetime->end = $data['end'];
    }
    return $lifetime;
  }

  static public function fromJSON(string $json) {
    return json_decode($json, true);
  }
}
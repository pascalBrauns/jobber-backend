<?php

namespace Jobber\Reactor\Type\Job;
require __DIR__ . '/../Enum.php';

use Jobber\Reactor\Type\Enum;

class Status extends Enum {
  const PLANNED = 'planned';
  const ACTIVE = 'active';
  const SUSPENDED = 'suspended';
  const FAILED = 'failed';
  const COMPLETED = 'completed';
  const CANCELED = 'canceled';

  static function isPending(string $status) {
    return in_array($status, [
        Status::PLANNED,
        Status::ACTIVE,
        Status::SUSPENDED
    ]);
  }
}

Status::register();
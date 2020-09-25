<?php

namespace Jobber\Reactor\Type\Job;

use Jobber\Reactor\Type\Enum;

class Type extends Enum {
  const TASK = 'task';
  const PIPELINE = 'pipeline';
  const BRIDGE = 'bridge';
}

Type::register();
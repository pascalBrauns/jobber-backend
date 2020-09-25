<?php

namespace Jobber;
require __DIR__ . '/Inbox/Inbox.php';
require __DIR__ . '/Storage/Storage.php';
require __DIR__ . '/Listener/Listener.php';
require __DIR__ . '/Grid.php';
require __DIR__ . '/Printer.php';
require __DIR__ . '/Type/Query.php';
require __DIR__ . '/Type/Blueprint.php';
require __DIR__ . '/Type/Message.php';
require __DIR__ . '/Type/Job/Job.php';
require __DIR__ . '/Type/DTO.php';
require __DIR__ . '/Cleaner/Cleaner.php';
use Event;
use Jobber\Reactor\Cleaner;
use Jobber\Reactor\Listener;
use Swoole;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Grid;

class Reactor {
  public static Reactor\Inbox $inbox;
  public Swoole\Process $process;
  public Reactor\Storage $storage;

  /**
   * Reactor constructor.
   * @param Event\Emitter[] $emitters
   */
  public function __construct(...$emitters) {
    Storage::setup();
    Reactor::$inbox = new Reactor\Inbox(...$emitters);
  }

  function listen(callable $work) {
    Grid::$work = $work;
    $this->process = new Swoole\Process(function(Swoole\Process $process) {
      $process->pop();
      Grid::setup();
      Listener::listen();
      Cleaner::run();
      Reactor::$inbox->listen();
    });
    $this->process->useQueue(4, 2);
    $this->process->start();
    $this->process->push(json_encode([]));
  }
}
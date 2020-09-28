<?php
namespace Jobber;
use Swoole;
use Jobber\Event;
use Jobber\Reactor\Cleaner;
use Jobber\Reactor\Listener;
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
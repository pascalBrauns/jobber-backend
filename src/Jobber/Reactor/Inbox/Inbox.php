<?php

namespace Jobber\Reactor;
require __DIR__ . '/Message.php';
use Swoole;
use Event;
use Jobber\Reactor\Type;

class Inbox extends Event\Emitter {
  public Swoole\Process $process;
  /**
   * @var Event\Emitter[] $external
   */
  private array $external;

  /**
   * Inbox constructor.
   * @param Event\Emitter[] $emitters
   */
  public function __construct(...$emitters) {
    $this->external = $emitters;
    $this->process = new Swoole\Process(function(Swoole\Process $process) {
      while(true) {
        sleep(1);
      }
    });
    $this->process->useQueue(3, 2);
    $this->process->start();
  }

  public function send(Type\Message $message) {
    $this->process->push($message->toJSON());
  }

  public function listen() {
    $message = new Type\Message;
    $message->subject = 'start';
    $message->payload = [];
    $this->send($message);
    go(function() {
      while (true) {
        $json = $this->process->pop();
        $message = Type\Message::fromJSON($json);
        $this->emit($message->subject, $message->payload);
        foreach ($this->external as $external) {
          $external->emit($message->subject, $message->payload);
        }
      }
    });
  }
}
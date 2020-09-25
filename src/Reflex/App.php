<?php

namespace Reflex;
use Throwable;
use Swoole;
use Swoole\Http\Request as SRequest;
use Swoole\Http\Response as SResponse;

class App extends Router {
  private int $timeout;

  public function __construct(int $timeout) {
    parent::__construct();
    $this->timeout = $timeout;
  }

  /**
   * @var Swoole\Process[] $processes
   */
  private array $processes = [];

  private function handle(Request $request, Response $response) {
    try {
      $this->pipe->handle($request, $response);
      if ($request->handled === false) {
        $response->status(404);
        $response->send('Resource not found');
      }
      else if ($response->finished === false) {
        $timeout = $this->timeout;
        Swoole\Timer::after(
            $timeout,
            function() use($response, $timeout) {
              $response->status(408);
              $response->send("Request timed out after $timeout ms");
            }
        );
      }
    }
    catch (Throwable $throwable) {
      $message = $throwable->getMessage();
      $response->status(500);
      $response->send("Internal server error: $message");
    }
  }

  function process(Swoole\Process $process) {
    $this->processes[] = $process;
  }

  function listen(int $port, string $host, callable $onStart) {
    $server = new Swoole\Http\Server($host, $port);
    foreach ($this->processes as $process) {
      $server->addProcess($process);
    }
    if (!is_null($onStart)) {
      $handle = fn(Swoole\Http\Server $server) => $onStart();
      $server->on("start", $handle);
    }
    $server->on(
        "request",
        fn(SRequest $sRequest, SResponse $sResponse) => $this->handle(
            new Request($sRequest->getData()),
            new Response($sResponse)
        )
    );
    $server->start();
  }

}
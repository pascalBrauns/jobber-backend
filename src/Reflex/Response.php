<?php

namespace Reflex;

use Swoole;

class Response {
  public bool $finished = false;
  private Swoole\Http\Response $response;

  public function __construct(Swoole\Http\Response $response) {
    $this->response = $response;
  }

  function type(string $type) {
    $this->response->setHeader("Content-Type", $type);
  }

  function status(int $code) {
    $this->response->setStatusCode($code);
  }

  function json(array $data) {
    $this->type('application/json');
    $this->end(json_encode($data));
  }

  function send(string $text) {
    $this->type('text/plain');
    $this->end($text);
  }

  function end(string $data) {
    $this->response->end($data);
    $this->finished = true;
  }
}
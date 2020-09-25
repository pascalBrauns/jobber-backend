<?php

namespace Jobber;

use Client;

class Request {
  static public function status(string $id, string $type, string $status) {
    $request = new Client\Request;
    $request->method = 'POST';
    $request->uri = "/job/$id/status";
    $request->options = [
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode([
            'id' => $id,
            'type' => $type,
            'status' => $status
        ])
    ];
    return $request;
  }

  static function progress(string $id, int $completed, int $pending) {
    $request = new Client\Request;
    $request->method = 'POST';
    $request->uri = "/job/$id/progress";
    $request->options = [
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode(['completed' => $completed, 'pending' => $pending])
    ];
    return $request;
  }

  static function log(string $id, string $text) {
    $request = new Client\Request;
    $request->method = 'PUT';
    $request->uri = "/job/$id/log";
    $request->options = [
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'body' => json_encode(['id' => $id, 'text' => $text])
    ];
    return $request;
  }
}
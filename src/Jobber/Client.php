<?php

namespace Jobber;
use GuzzleHttp;
use Jobber\Reactor\Storage;
use Throwable;

class Client {
  private static function instance() {
    return new GuzzleHttp\Client([
        'headers' => [
            'Content-Type' => 'application/json'
        ]
    ]);
  }

  static function create(string $host, string $port, string $id) {
    try {
      $response = Client::instance()->put("$host:$port/job/$id", [
          'body' => Storage::$job->get($id)->toJSON()
      ]);
      print_r($response->getBody()->getContents());
    }
    catch(Throwable $throwable) {
      print_r($throwable->getMessage());
    }
  }

  static function lifetime(string $host, int $port, string $id) {
    try {
      $response = Client::instance()->post("$host:$port/job/$id/lifetime", [
          'body' => Storage::$job->get($id)->lifetime->toJSON()
      ]);
      print_r($response->getBody()->getContents());
    }
    catch(Throwable $throwable) {
      print_r($throwable->getMessage());
    }
  }

  static function progress(string $host, int $port, string $id) {
    try {
      $response = Client::instance()->post("$host:$port/job/$id/progress", [
          'body' => Storage::$job->get($id)->progress->toJSON()
      ]);
      print_r($response->getBody()->getContents());
    }
    catch(Throwable $throwable) {
      print_r($throwable->getMessage());
    }
  }

  static function status(string $host, int $port, string $id) {
    try {
      $response = Client::instance()->post("$host:$port/job/$id/status", [
          'body' => json_encode([
              'status' => Storage::$job->get($id)->status
          ])
      ]);
      print_r($response->getBody()->getContents());
    }
    catch(Throwable $throwable) {
      print_r($throwable->getMessage());
    }
  }

  static function log(string $host, int $port, string $id) {
    try {
      $logs = Storage::$job->get($id)->logs;
      $response = Client::instance()->post("$host:$port/job/$id/log", [
          'body' => json_encode([
              'message' => $logs[count($logs) -1]
          ])
      ]);
      print_r($response->getBody()->getContents());
    }
    catch (Throwable $throwable) {
      print_r($throwable->getMessage());
    }
  }

  static function removed(string $host, int $port, string $id) {
    try {
      $response = Client::instance()->delete("$host:$port/blueprint/$id");
      print_r($response->getBody()->getContents());
    }
    catch (Throwable $throwable) {
      print_r($throwable->getMessage());
    }
  }
}
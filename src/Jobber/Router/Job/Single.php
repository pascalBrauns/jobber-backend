<?php

namespace Jobber\Router\Job;
use Jobber\Reactor;
use Jobber\Reactor\Storage;
use Jobber\Reactor\Type\DTO;
use Reflex;
use Reflex\Request;
use Reflex\Response;

class Single {
  private static function job() {
    $router = Reflex::router();
    $router->put('/job', function(Request $request, Response $response) {
      $query = Reactor\Type\Query::fromJSON($request->body());
      $blueprint = Reactor\Type\Blueprint::fromQuery($query);
      $message = Reactor\Inbox\Message::create($blueprint);
      Reactor::$inbox->send($message);
      $response->json($blueprint->toArray());
    });
    $router->get('/job/:id', function(Request $request, Response $response) {
      $id = $request->parameter('id');
      $blueprint = Storage::$job->blueprint($id);
      $dto = DTO::fromBlueprint($blueprint);
      $response->json($dto);
    });
    return $router;
  }

  private static function status() {
    $router = Reflex::router();
    $router->get('/job/:id/status', function(Request $request, Response $response) {
      $job = Reactor\Storage::$job->get($request->parameter('id'));
      $response->json([
          'status' => $job->status
      ]);
    });
    $router->post('/job/:id/status', function(Request $request, Response $response) {
      $id = $request->parameter('id');
      $body = json_decode($request->body(), true);
      $status = $body['status'];
      if ($status === Reactor\Type\Job\Status::SUSPENDED) {
        Reactor::$inbox->send(Reactor\Inbox\Message::suspend($id));
      }
      else if ($status === Reactor\Type\Job\Status::ACTIVE) {
        Reactor::$inbox->send(Reactor\Inbox\Message::resume($id));
      }
      else if ($status === Reactor\Type\Job\Status::CANCELED) {
        Reactor::$inbox->send(Reactor\Inbox\Message::cancel($id));
      }
      $response->json(['success' => true]);
    });
    return $router;
  }

  private static function progress() {
    $router = Reflex::router();
    $router->get('/job/:id/progress', function(Request $request, Response $response) {
      $job = Reactor\Storage::$job->get($request->parameter('id'));
      $response->json($job->progress->toArray());
    });
    return $router;
  }

  private static function lifetime() {
    $router = Reflex::router();
    $router->get('/job/:id/lifetime', function(Request $request, Response $response) {
      $job = Reactor\Storage::$job->get($request->parameter('id'));
      $response->json($job->lifetime->toArray());
    });
    return $router;
  }

  static function router() {
    $router = Reflex::router();
    $router->use(Single::job());
    $router->use(Single::status());
    $router->use(Single::progress());
    $router->use(Single::lifetime());
    return $router;
  }
}
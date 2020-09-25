<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/Console.php';
require __DIR__ . '/Event/Emitter.php';
require __DIR__ . '/Reflex/Reflex.php';
require __DIR__ . '/Jobber/Jobber.php';
use Jobber\Environment;
use Jobber\Reactor\Grid\Task;
use Jobber\Spreader;

$host = '0.0.0.0';
$port = Environment::$port;

Jobber::reactor(...Spreader::fromEnvironment())
    ->listen(fn(Task $task) => Jobber\Simulation::work($task));

$app = Reflex::app();
$app->use(Jobber::router());

$app->listen(
    $port, $host,
    fn() => print_r("App listening on $host:$port\n")
);
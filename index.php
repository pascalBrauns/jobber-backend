<?php
require __DIR__ . '/src/Autoloader.php';
use Jobber\Environment;
use Jobber\Reactor\Grid\Task;
use Jobber\Spreader;

Environment::load(__DIR__.'/.env');

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
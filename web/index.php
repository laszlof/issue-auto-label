<?php

require_once '../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));


$app->post('/', function() use ($app) {
  $hook = new \Issue\Hook($app);
  if ($hook->isValid()) {
    return $hook->process();
  }
});

$app->run();

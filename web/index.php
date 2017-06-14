<?php

require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

$app->post('/', function(Request $request) use ($app) {
  $hook = new \Issue\Hook($app, $request);
  if ($hook->isValid()) {
    $hook->process();
  }
  return 'Done';
});

$app->run();

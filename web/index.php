<?php

require_once '../vendor/autoload.php';

use \Symfony\Component\HttpFoundation\Request;
use \Silex\Application;
use \Silex\Provider\MonologServiceProvider;
use \Issue\Hook;

$app = new Application();
$app['debug'] = false;

// Register the monolog logging service
$app->register(new MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Handle the post request
$app->post('/', function(Request $request) use ($app) {
  $hook = new Hook($app, $request);
  if ($hook->isValid()) {
    $hook->process();
  }
  return 'Done';
});

$app->run();

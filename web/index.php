<?php

require_once '../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;


$app->get('/', function() use ($app) {
  return 'Hello World';
});

$app->run();

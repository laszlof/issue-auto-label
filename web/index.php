<?php

require_once '../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;


$app->get('/', function() use ($app) {
  $hook = new \Issue\Hook();
  if ($hook->isValid()) {
    return $hook->process();
  }
});

$app->run();

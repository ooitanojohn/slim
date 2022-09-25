<?php
session_start();

use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();


// Add error middleware
require_once __DIR__ . '/../bootstrappers.php';
// Add route
require_once __DIR__ . '/../route.php';

$app->run();

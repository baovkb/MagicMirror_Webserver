<?php

use MM\cores\Route;
use MM\controllers\HomeController;
use MM\controllers\E404Controller;

$route = new Route();

//register routing
$route->get('/', [HomeController::class, 'index']);
$route->get('/404', [E404Controller::class, 'index']);

$route->run();
?>
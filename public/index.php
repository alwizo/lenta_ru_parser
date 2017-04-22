<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname( __DIR__ ) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('show/{id:\d+}', ['controller' => 'Home', 'action' => 'show']);
$router->add('parser/download', ['controller' => 'Parser', 'action' => 'parseNews']);
$router->add('parser/daily_news', ['controller' => 'Parser', 'action' => 'dailyNewstoCsv']);
$router->add('parser/test', ['controller' => 'Parser', 'action' => 'test']);

$router->dispatch($_SERVER['QUERY_STRING']);

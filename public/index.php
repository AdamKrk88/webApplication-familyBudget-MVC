<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Sessions
 */
session_start();


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Login', 'action' => 'new']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Logout', 'action' => 'destroy']);
$router->add('{controller}/{action}');
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add('settings/email-activate/{token:[\da-f]+}', ['controller' => 'Settings', 'action' => 'activateNewEmail']);
$router->add('settings/display-settings/{option:user|income|expense}', ['controller' => 'Settings', 'action' => 'displaySettings']);
$router->add('settings/display-settings/user/{option:change-name|change-email|change-password|profile}', ['controller' => 'Settings', 'action' => 'displaySettings']);  

$router->dispatch($_SERVER['QUERY_STRING']);

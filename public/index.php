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
$router->add('settings/display-settings/{option:user|income|expense}', ['controller' => 'Settings', 'action' => 'displaySettingsOptions']);
$router->add('settings/display-settings/user/{option:change-name|change-email|change-password|profile}', ['controller' => 'Settings', 'action' => 'displaySettingsUser']);  
$router->add('settings/display-settings/expense/{option:add-category|add-payment|delete-category|delete-payment|expense-list|change-category|change-payment|change-amount|change-date|change-comment|delete-item}', ['controller' => 'Settings', 'action' => 'displaySettingsExpense']);  
$router->add('settings/display-settings/income/{option:add-category|delete-category|income-list|change-category|change-amount|change-date|change-comment|delete-item}', ['controller' => 'Settings', 'action' => 'displaySettingsIncome']);

$router->dispatch($_SERVER['QUERY_STRING']);

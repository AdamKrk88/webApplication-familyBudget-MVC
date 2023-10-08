<?php

namespace App\Controllers;

use \Core\View;

/**
 * Login controller
 *
 * PHP version 7.2.0
 */

class Login extends \Core\Controller 
{
    /**
     * Show the login page 
     * 
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Login/login.html');
    }
}
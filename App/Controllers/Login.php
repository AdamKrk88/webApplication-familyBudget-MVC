<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;
use \App\Auth;

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

    public function blockAccessAction()
    {
        View::renderTemplate('no_authorization.html');
    }

    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        
    //    $remember_me = isset($_POST['remember_me']);

        if ($user) {

            Auth::login($user);

     //       Flash::addMessage('Login successful');

            $this->redirect('/menu/display');

        } else {

            Flash::addMessage('Login data incorrect. Please try again', Flash::ORANGE);

            View::renderTemplate('Login/login.html', [
                'email' => $_POST['email']
                
            ]);
        }
    }
}
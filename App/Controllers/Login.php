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

class Login extends OpenAccess 
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
        
        $remember_me = isset($_POST['remember-me']);

        if ($user) {

            Auth::login($user, $remember_me);

     //       Flash::addMessage('Login successful');

            $this->redirect('/menu/display');

        } else {

            Flash::addMessage('Login data incorrect. Please try again', Flash::ORANGE);

            View::renderTemplate('Login/login.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
                
            ]);
        }
    }


    /**
     * Show a "logged out" flash message and redirect to the homepage. Necessary to use the flash messages
     * as they use the session and at the end of the logout method (destroyAction) the session is destroyed
     * so a new action needs to be called in order to use the session.
     *
     * @return void
     */
    public function showLogoutMessageAction()
    {
        Flash::addMessage('Logout successful', Flash::ORANGE);
        
        $this->redirect('/');
    }

}
<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

/**
 * Signup controller
 * 
 * PHP version 7.2.0
 */
class Signup extends OpenAccess
{

     /**
     * Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Signup/signup.html');
    }


    public function createAction()
    {
        // var_dump($_POST);
        
        $user = new User($_POST);

        if ($user->save()) {

            $user->sendActivationEmail();
       //     Flash::addMessage('Your registration is successful. You can log in', Flash::ORANGE);
            $this->redirect('/signup/success');

        } else {

            View::renderTemplate('Signup/fail.html', [
                'user' => $user
            ]);

        }
        
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Signup/success_before_activation.html');
    }

    /**
     * Activate a new account
     *
     * @return void
     */
    public function activateAction()
    {   
        $token = $this->route_params['token'];
        User::activateAccount($token);    
       
        if (User::returnAccountStatus($token)) 
        {
            Flash::addMessage('Account activated. You can log into the application', Flash::ORANGE);
        }
        else 
        {
            Flash::addMessage('Try to log in. If it failed, please go to registration page', Flash::ORANGE);
        }

        User::clearActivationToken($token);
        
        View::renderTemplate('Login/login.html');  
            
        //$this->redirect('/signup/activated');
    }
}
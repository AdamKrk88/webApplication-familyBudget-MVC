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
class Signup extends \Core\Controller
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

         //   $user->sendActivationEmail();
            Flash::addMessage('Your registration is successful. You can log in', Flash::ORANGE);
            $this->redirect('/login/new');

        } else {

            View::renderTemplate('Signup/fail.html', [
                'user' => $user
            ]);

        }
        
    }
}
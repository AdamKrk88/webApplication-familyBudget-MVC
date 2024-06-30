<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;


/**
 * Password controller
 *
 * PHP version 7.2.0
 */
class Password extends OpenAccess
{
    /**
     * Show the forgotten password page
     *
     * @return void
     */
    public function forgotAction()
    {
        View::renderTemplate('Password/forgot.html');
    }

    public function requestResetAction()
    {    
        if (isset($_POST['email']))
            {
            $isEmailSent = User::sendPasswordReset($_POST['email']);
            
            if($isEmailSent) 
            {
                View::$twig = null;
                Flash::addMessage('Please check your email to reset the password', Flash::ORANGE);
                
                View::renderTemplate('Login/login.html', [
                    'email' => $_POST['email']
                ]);
            }
            else
            {
                View::$twig = null;
                Flash::addMessage('Password reset process failed', Flash::ORANGE);

                View::renderTemplate('Login/login.html');
            }
        }
        else
        {
            $this->redirect('/');
        }


    //    View::renderTemplate('Password/reset_requested.html');
    }

    /**
     * Show the reset password form
     *
     * @return void
     */
    public function resetAction()
    {
        $token = $this->route_params['token'];

        $user = $this->getUserOrExit($token);

        View::renderTemplate('Password/reset.html', [
            'token' => $token
        ]);
    }

    /**
     * Reset the user's password
     *
     * @return void
     */
    public function resetPasswordAction()
    {
        $token = $_POST['token'];

        $user = $this->getUserOrExit($token);

        if ($user->resetPassword($_POST['password'])) {

            //echo "password valid";
            Flash::addMessage('Password reset successfully', Flash::ORANGE);
            View::renderTemplate('Login/login.html');
        
        } else {

            View::renderTemplate('Password/reset_failed.html', [
                'token' => $token,
                'user' => $user
            ]);
        }
    }
    

    /**
     * Find the user model associated with the password reset token, or end the request with a message
     *
     * @param string $token Password reset token sent to user
     *
     * @return mixed User object if found and the token hasn't expired, null otherwise
     */
    protected function getUserOrExit($token)
    {
        $user = User::findByPasswordReset($token);

        if ($user) {

            return $user;

        } else {

            View::renderTemplate('Password/link_invalid.html');
            exit;

        }
    }
}
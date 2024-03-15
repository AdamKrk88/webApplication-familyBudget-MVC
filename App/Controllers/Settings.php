<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

/**
 * Settings controller
 *
 * PHP version 7.2.0
 */
class Settings extends \Core\Controller
{
    public function displaySettingsAction()
    {   
        if (isset($this->route_params['option'])) 
        {
            $option = $this->route_params['option'];

            if ($option === 'user')
            {
                View::renderTemplate('Settings/user.html');
            }
            elseif ($option === 'change-name')
            {
                View::renderTemplate('Settings/user_change_name.html');
            }
            elseif ($option === 'change-email')
            {
                View::renderTemplate('Settings/user_change_email.html');
            }
            elseif ($option === 'change-password')
            {
                View::renderTemplate('Settings/user_change_password.html');
            }
            elseif ($option === 'profile')
            {
                $user = User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

                if ($user)
                {
                    View::renderTemplate('Settings/user_profile.html', [
                        'username' => $user->username,
                        'email' => $user->email  
                    ]);
                }
                else
                {
                    View::renderTemplate('Settings/user_profile.html');
                }
            }
        }
        else
        {
            View::renderTemplate('Settings/options.html');  
        }  
    }

    public function changeUsernameAction() 
    {
        $user = User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

        $user->username = $_POST['new-username'];

        $user->validateUsername();

     //   var_dump($user->errors);

        if (empty($user->errors))
        {
            if ($user->changeUsername())
            {
                Flash::addMessage('Username changed successfully', Flash::ORANGE);
            }
            else
            {
                Flash::addMessage('Database error', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage($user->errors[0], Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/user/change-name');

    }

    public function changeEmailAction() 
    {
        $user = User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

        $user->email_change = $_POST['new-email'];

        $user->validateNewEmail();

     //   var_dump($user->errors);

        if (empty($user->errors))
        {
            if($user->startEmailChange())
            {
                $user->sendEmailChangeInformationToUserNewMailbox();
                Flash::addMessage('Please check your new email and confirm the change', Flash::ORANGE);
            }
            else
            {
                Flash::addMessage('Database error', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage($user->errors[0], Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/user/change-email');

    }

    public function activateNewEmail()
    {
        $token = $this->route_params['token'];
        $user = User::getUserNewEmail($token);  

        if ($user) 
        {  
            if ($user->updateEmail($token))
            {
                Flash::addMessage('Email changed');
            }
            else
            {
                Flash::addMessage('Database error. Email not changed');
            }  
        }        
        else 
        {
            Flash::addMessage('Match with database record not found');
        }

     
        User::clearEmailChangeToken($token);
        
        View::renderTemplate('Settings/email_change_website.html');   

   //     Flash::addMessage('Match with database record not found');
   //     View::renderTemplate('Settings/email_change_website.html');
    }

    public function changePasswordAction()
    {
        $user = User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

        $user->new_password = $_POST['new-password'];

        $user->validateNewPassword();

     //   var_dump($user->errors);

        if (empty($user->errors))
        {
            if ($user->checkIfPasswordIsDifferent())
            {
                if ($user->changePasswordByLoggedInUser())
                {
                    Flash::addMessage('Password changed successfully', Flash::ORANGE);
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                }
            }
            else
            {
                Flash::addMessage('No change. This is your current password', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage($user->errors[0], Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/user/change-password');
    }
}
<?php

namespace App;

use \App\Models\User;

/**
 * Authentication
 *
 * PHP version 7.2.0
 */
class Auth
{
    public static function login($user)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

       /*
        if ($remember_me) {

            if ($user->rememberLogin()) {

                setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
                
            }
        }

        */
    }


    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {

            return User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

        } else {

            return false;
        }
    }

}
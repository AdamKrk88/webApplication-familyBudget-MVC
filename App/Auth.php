<?php

namespace App;

use \App\Models\User;
use \App\Models\RememberedLogin;

/**
 * Authentication
 *
 * PHP version 7.2.0
 */
class Auth
{
    public static function login($user, $remember_me)
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user->id;

        if ($remember_me) {

            if ($user->rememberLogin()) {

                setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
                
            }
        }
    }


    public static function getUser()
    {
        if (isset($_SESSION['user_id'])) {

            return User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

        } else {

            return static::loginFromRememberCookie();
        }
    }

    protected static function loginFromRememberCookie()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if ($cookie) {

            $remembered_login = RememberedLogin::findByToken($cookie);

            //if ($remembered_login) {
            if ($remembered_login && ! $remembered_login->hasExpired()) {

                $user = $remembered_login->getUser();

                static::login($user, false);

                return $user;
            }
        }
    }



}
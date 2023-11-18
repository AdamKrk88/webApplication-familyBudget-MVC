<?php

namespace App\Controllers;

use \App\Auth;

/**
 * Logout controller
 *
 * PHP version 7.2.0
 */

class Logout extends Authenticated
{
    /**
     * Log out a user
     *
     * @return void
     */
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/showLogoutMessage');
    }

}
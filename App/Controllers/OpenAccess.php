<?php

namespace App\Controllers;

/**
 * OpenAccess controller
 *
 * PHP version 7.2.0
 */
abstract class OpenAccess extends \Core\Controller
{
    /**
     * Move user to menu if she/he is logged in
     *
     * @return void
     */
    protected function before()
    {
        $this->openAccessForLoggedUser();
    }
}

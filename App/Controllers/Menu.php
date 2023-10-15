<?php

namespace App\Controllers;

use \Core\View;

/**
 * Menu controller
 *
 * PHP version 7.2.0
 */
class Menu extends Authenticated
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function displayAction()
    {
        View::renderTemplate('Menu/index.html');
    }
}

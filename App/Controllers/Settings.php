<?php

namespace App\Controllers;

use \Core\View;

/**
 * Settings controller
 *
 * PHP version 7.2.0
 */
class Settings extends \Core\Controller
{
    public function displaySettings()
    {
        View::renderTemplate('Settings/options.html');  
    }
}
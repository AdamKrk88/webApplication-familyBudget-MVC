<?php

namespace App\Controllers;

use \Core\View;

/**
 * Block access to website for non-authorized users
 *
 * PHP version 7.2.0
 */

class Block extends \Core\Controller
{
   
    public function stopDisplayNoJavaScript()
    {
        View::renderTemplate('Block/javaScript_blocked.html');
    }
    
}
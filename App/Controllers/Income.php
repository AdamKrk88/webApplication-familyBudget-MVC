<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;

/**
 * Income controller
 *
 * PHP version 7.2.0
 */
class Income extends Authenticated
{

    public function displayIncomeFormAction()
    {
        $categories = User::returnCategoriesForIncome($_SESSION['user_id']);
        View::renderTemplate('Income/income_form.html', [
            'categories' => $categories
        ]);
    }

    public function processAjax() 
    {

    }
}
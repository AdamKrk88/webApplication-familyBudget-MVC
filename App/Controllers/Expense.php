<?php

namespace App\Controllers;

use \App\Models\User;
use \Core\View;

/**
 * Expense controller
 *
 * PHP version 7.2.0
 */
class Expense extends Authenticated
{

    public function displayExpenseFormAction()
    {
        $categories = User::returnCategoriesForExpense($_SESSION['user_id']);
        $payments = User::returnPaymentMethods($_SESSION['user_id']);
        View::renderTemplate('Expense/expense_form.html', [
            'categories' => $categories,
            'payments' => $payments
        ]);
    }

}
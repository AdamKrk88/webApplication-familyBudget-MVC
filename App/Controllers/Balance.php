<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\CashFlow;

/**
 * Balance review controller
 *
 * PHP version 7.2.0
 */
class Balance extends Authenticated
{

    public function displayBalanceReview()
    {
    //    $categoryTotalAmountValueForExpense = CashFlow::getCategoryAndRelatedAmountForExpense($_SESSION['user_id']); 
        $categoryTotalAmountValueForExpense = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'],'isCurrentMonthDate','expense'); 
        $categoryTotalAmountValueForIncome = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'],'isCurrentMonthDate','income');
        $totalExpense = CashFlow::getTotalExpense($_SESSION['user_id'], $categoryTotalAmountValueForExpense);
        $totalIncome = CashFlow::getTotalIncome($_SESSION['user_id'], $categoryTotalAmountValueForIncome);
        $balance = CashFlow::getTotalBalance($totalIncome, $totalExpense);
        $numberOfCategoriesInFirstTable = floor(count($categoryTotalAmountValueForExpense) / 2) + count($categoryTotalAmountValueForExpense) % 2;
        $numberOfCategoriesInSecondTable = floor(count($categoryTotalAmountValueForExpense) / 2);  

        View::renderTemplate('Balance/review.html', [
            'categoryTotalAmountValueForExpense' => $categoryTotalAmountValueForExpense,
            'numberOfCategoriesInFirstTable' => $numberOfCategoriesInFirstTable,
            'numberOfCategoriesInSecondTable' => $numberOfCategoriesInSecondTable,
            'balance' => $balance,
            'totalExpense' => $totalExpense
        ]);  

   //    var_dump(false !== true);
     
        
    }
    

}
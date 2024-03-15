<?php

namespace App\Controllers;

use \App\Models\User;
use \App\Models\CashFlow;
use \App\Auth;
use \App\Flash;

/**
 * AJAX - used to send asynchronous HTTP requests to the server 
 *
 * PHP version 7.2.0
 */

class Ajax extends \Core\Controller
{
    /**
     * Be available only for ajax request
     *
     * @return void
     */
    protected function before()
    { 
        if (!isset($_POST['ajax']) || $_POST['ajax'] !== "true")
        {
    
           throw new \Exception("Stop processing the request");

        }
    }

    /** 
     * Comment verification 
     * @param string $comment Comment provided by user. Comment is optional
     * 
     * @return string error message or empty string
     */ 
    public static function checkComment($comment) 
    {
        $error = "";
        if ($comment != '' || strlen($comment) != 0) {
          if (!preg_match("/^[a-z0-9\040\.\-\/]+$/i",$comment)) {    // \x5C backslash
            $error = "Only letters, numbers, space, forward slash, period and dash allowed in the comment";
            return $error;
          }
        }
        return $error;       
      }

    /**
     * Take data from first income / expense form as result of ajax request
     * 
     * @return void
     */
    public function processFirstFormAction()
    {
        $_SESSION['amount'] = User::testInput($_POST['amount']);
        $_SESSION['date'] = User::testInput($_POST['date']);

        if (isset($_POST['payment']))
        {
            $_SESSION['payment'] = User::testInput($_POST['payment']);
        }
    }

    /**
     * Take data from second form and insert information about income / expense into database
     * 
     * @return void
     */
    public function processSecondFormAction()
    {
        $_SESSION['category'] = User::testInput($_POST['category']);
        $_SESSION['comment'] = User::testInput($_POST['comment']);

        $error = static::checkComment($_SESSION['comment']);

        if (!$error)
        {
            if (isset($_SESSION['payment'])) 
            {
                $isAdded = CashFlow::addExpense($_SESSION['user_id'], $_SESSION['amount'], $_SESSION['date'], $_SESSION['payment'], $_SESSION['category'], $_SESSION['comment']);
            }
            else 
            {
                $isAdded = CashFlow::addIncome($_SESSION['user_id'], $_SESSION['amount'], $_SESSION['date'],  $_SESSION['category'], $_SESSION['comment']);
            }

            if (!$isAdded) 
            {
                $error = 'Something went wrong with database';
                echo json_encode($error);
            }
        }
        else
        {
            echo json_encode($error);
        }

        unset($_SESSION['amount']);
        unset($_SESSION['date']);
        unset($_SESSION['category']);
        unset($_SESSION['comment']);

        if (isset($_SESSION['payment'])) 
        {
            unset($_SESSION['payment']);
        }

    }

    public function switchBetweenIncomeExpenseAction() 
    {
        if (!$_POST['isModal']) 
        {
            $categoryTotalAmountValue = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], $_POST['incomeOrExpense']);
        }
        else 
        {
            $categoryTotalAmountValue = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], $_POST['incomeOrExpense'], $_POST['startDateFromModal'], $_POST['endDateFromModal']);    
        }

        echo json_encode($categoryTotalAmountValue);
    }


    public function calculateTotalBalanceAction() 
    {
        if (!$_POST['isModal']) {
            $categoryTotalAmountValueForIncome = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], "income");
            $categoryTotalAmountValueForExpense = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], "expense");
            $totalIncome = CashFlow::getTotalIncome($_SESSION['user_id'], $categoryTotalAmountValueForIncome);
            $totalExpense = CashFlow::getTotalExpense($_SESSION['user_id'], $categoryTotalAmountValueForExpense);

            $balance = CashFlow::getTotalBalance($totalIncome, $totalExpense);
        }
        else 
        {
            $categoryTotalAmountValueForIncome = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], "income", $_POST['startDateFromModal'], $_POST['endDateFromModal']);
            $categoryTotalAmountValueForExpense = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], "expense", $_POST['startDateFromModal'], $_POST['endDateFromModal']);
            $totalIncome = CashFlow::getTotalIncome($_SESSION['user_id'], $categoryTotalAmountValueForIncome);
            $totalExpense = CashFlow::getTotalExpense($_SESSION['user_id'], $categoryTotalAmountValueForExpense);

            $balance = CashFlow::getTotalBalance($totalIncome, $totalExpense);  
        }

        echo json_encode($balance);
    }

    public function updatePieChartAction() 
    {

        $dataToUpdatePieChart =[];
        $incomeCategories = [];
        $percentagePerCategory = [];
        $singlePercentage = 0;
        $backgroundColorForPieChart = [];

        if ($_POST['expenseOrIncome'] == "Income") 
        {
            if ($_POST['isModal']) 
            {
                $categoryTotalAmountValue = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], "income", $_POST['startDateFromModal'], $_POST['endDateFromModal']);
                $incomeExpense = CashFlow::getTotalIncome($_SESSION['user_id'], $categoryTotalAmountValue);  
            }
            else 
            {
                $categoryTotalAmountValue = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], "income");
                $incomeExpense = CashFlow::getTotalIncome($_SESSION['user_id'], $categoryTotalAmountValue);
            }

            $categoryTotalAmountValueLength = count($categoryTotalAmountValue);     
        }
        elseif ($_POST['expenseOrIncome'] == "Expense") 
        {
            if ($_POST['isModal']) 
            {

                $categoryTotalAmountValue = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], "expense", $_POST['startDateFromModal'], $_POST['endDateFromModal']);
                $incomeExpense = CashFlow::getTotalExpense($_SESSION['user_id'], $categoryTotalAmountValue);
            }
            else 
            {

                $categoryTotalAmountValue = CashFlow::getCategoryAndRelatedAmount($_SESSION['user_id'], $_POST['timePeriod'], "expense");
                $incomeExpense = CashFlow::getTotalExpense($_SESSION['user_id'], $categoryTotalAmountValue);
            }

            $categoryTotalAmountValueLength = count($categoryTotalAmountValue);    
        }

        for ($i = 0; $i < $categoryTotalAmountValueLength; $i++) 
        {
            $incomeCategories[$i] = $categoryTotalAmountValue[$i][0];
            $singlePercentage = ((double)$categoryTotalAmountValue[$i][1]) / $incomeExpense;
            $percentagePerCategory[$i] = round($singlePercentage * 100, 2);

            switch ($i) 
            {
                case 0:
                    $backgroundColorForPieChart[$i] = "#ffccff";
                    break;
                case 1:
                    $backgroundColorForPieChart[$i] = "#bf80ff";
                    break;
                case 2:
                    $backgroundColorForPieChart[$i] = "#ff80ff";
                    break;
                case 3:
                    $backgroundColorForPieChart[$i] = "#df9fbf";
                    break;
                case 4:
                    $backgroundColorForPieChart[$i] = "#ff80bf";
                    break;
                case 5:
                    $backgroundColorForPieChart[$i] = "#ff80aa";
                    break;
                case 6:
                    $backgroundColorForPieChart[$i] = "#df9f9f";
                    break;
                case 7:
                    $backgroundColorForPieChart[$i] = "#ff8080";
                    break;
                case 8:
                    $backgroundColorForPieChart[$i] = "#ffbf80";
                    break;
                case 9:
                    $backgroundColorForPieChart[$i] = "#ffdf80";
                    break;
                case 10:
                    $backgroundColorForPieChart[$i] = "#dfff80";
                    break;
                case 11:
                    $backgroundColorForPieChart[$i] = "#80ff80";
                    break;
                case 12:
                    $backgroundColorForPieChart[$i] = "#80ffe5";
                    break;
                case 13:
                    $backgroundColorForPieChart[$i] = "#80ccff";
                    break;
                case 14:
                    $backgroundColorForPieChart[$i] = "#8080ff";
                    break;
                case 15:
                    $backgroundColorForPieChart[$i] = "#b3b3cc";
                    break;
                case 16:
                    $backgroundColorForPieChart[$i] = "#9fbfdf";
                    break;
                case 17:
                    $backgroundColorForPieChart[$i] = "#80bfff";
                    break;
            }
        }

        $dataToUpdatePieChart['incomeCategories'] = $incomeCategories;
        $dataToUpdatePieChart['percentagePerCategory'] = $percentagePerCategory;
        $dataToUpdatePieChart['backgroundColorForPieChart'] = $backgroundColorForPieChart;

        echo json_encode($dataToUpdatePieChart);
    }

    public function provideAllItemsForExpenseOrIncomeAction() {
        if ($_POST['expenseOrIncome'] == "Income") 
        {
            if ($_POST['isModal']) 
            {
                $dataToUpdateFirstPage = CashFlow::getAllItemsForExpenseOrIncome($_SESSION['user_id'], $_POST['timePeriod'], "income", $_POST['startDateFromModal'], $_POST['endDateFromModal']);   
            }
            else 
            {
                $dataToUpdateFirstPage = CashFlow::getAllItemsForExpenseOrIncome($_SESSION['user_id'], $_POST['timePeriod'], "income");
            }
        }

        else if ($_POST['expenseOrIncome'] == "Expense") 
        {
            if ($_POST['isModal']) 
            {
                $dataToUpdateFirstPage = CashFlow::getAllItemsForExpenseOrIncome($_SESSION['user_id'], $_POST['timePeriod'], "expense", $_POST['startDateFromModal'], $_POST['endDateFromModal']);
            }
            else 
            {
                $dataToUpdateFirstPage = CashFlow::getAllItemsForExpenseOrIncome($_SESSION['user_id'], $_POST['timePeriod'], "expense");
            }
        }

        echo json_encode($dataToUpdateFirstPage);
    }

    public function deleteAccount()
    {
        $isDeleted = User::deleteUserAccount($_SESSION['user_id']);
        $deletionResult = $isDeleted ? true : false;
        
        if ($deletionResult)
        {
            Auth::logout();
        }
        
        echo json_encode($deletionResult);
    }

}
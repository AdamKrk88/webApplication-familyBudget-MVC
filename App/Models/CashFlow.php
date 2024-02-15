<?php

namespace App\Models;

use PDO;
use \App\Date;


/**
 * Cash flow model - incomes, expenses, payment options
 *
 * PHP version 7.2.0
 */
class CashFlow extends \Core\Model
{
    private static function checkIfNumberHasDecimalPart($number) {
        return is_numeric($number) && floor($number) != $number;
    }

    private static function getDecimal($number) {
        if ($number >= 0) {   
            return $number - floor($number); 
        }
        else {
            return ceil($number) - $number;
        }
    }

    private static function formatNumberInBudget($number) {
        if (self::checkIfNumberHasDecimalPart($number)) {
            return number_format($number, 2,'.','');
        }
        else {
            return number_format($number, 0, '.','');
        }
    }



     /**
     * Add income to database
     *
     * @param string $userId ID of user logged in application
     * @param string $amount income amount provided by user
     * @param string $date date when income took place
     * @param string $category income category
     * @param string $comment optional comment for the income
     *
     * @return void
     */
    public static function addIncome($userId, $amount, $date, $category, $comment)
    {
        $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment)
        VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':income_category_assigned_to_user_id', $category, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindValue(':date_of_income', $date, PDO::PARAM_STR);
        $stmt->bindValue(':income_comment', $comment, PDO::PARAM_STR);

        return $stmt->execute();
    }

     /**
     * Add expense to database
     *
     * @param string $userId ID of user logged in application
     * @param string $amount expense amount provided by user
     * @param string $date date when expense took place
     * @param string $payment payment method used to cover expense
     * @param string $category expense category
     * @param string $comment optional comment for the expense
     *
     * @return void
     */
    public static function addExpense($userId, $amount, $date, $payment, $category, $comment)
    {
        $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment)
        VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':expense_category_assigned_to_user_id', $category, PDO::PARAM_STR);
        $stmt->bindValue(':payment_method_assigned_to_user_id', $payment, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindValue(':date_of_expense', $date, PDO::PARAM_STR);
        $stmt->bindValue(':expense_comment', $comment, PDO::PARAM_STR);

        return $stmt->execute();
    }

    private static function sumAmountsPerCategory(&$categoryAndAmountsArray, $dateCheckFunctionName, $incomeOrExpense, $dateFromModalFrom="0", $dateFromModalTo="0") {
        $categoryKeyTotalAmountValue = [];
        $dateCheckFunctionName = '\App\Date::' . $dateCheckFunctionName;
        $dateArgumentNameForCashFlow = 'date_of_' . $incomeOrExpense;
        $categoryArgumentNameForCashFlow = $incomeOrExpense . '_category_assigned_to_user_id';
     
        if ($dateFromModalFrom=="0" && $dateFromModalTo=="0") 
        {
            foreach($categoryAndAmountsArray as $singleCashFlow) 
            {
                if (call_user_func($dateCheckFunctionName, "$singleCashFlow[$dateArgumentNameForCashFlow]")) 
                {
                    if(array_key_exists($singleCashFlow[$categoryArgumentNameForCashFlow], $categoryKeyTotalAmountValue)) {
                        $categoryKeyTotalAmountValue[$singleCashFlow[$categoryArgumentNameForCashFlow]] = round((double)$categoryKeyTotalAmountValue[$singleCashFlow[$categoryArgumentNameForCashFlow]] + (double)$singleCashFlow['amount'],2);
                    }
                    else {
                        $categoryKeyTotalAmountValue[$singleCashFlow[$categoryArgumentNameForCashFlow]] = (double)$singleCashFlow['amount'];
                    }
                }
            }
        }
        elseif ($dateFromModalFrom!="0" && $dateFromModalTo!="0" && strtotime($dateFromModalFrom) && strtotime($dateFromModalTo)) 
        {
            foreach($categoryAndAmountsArray as $singleCashFlow) 
            {
                if (call_user_func($dateCheckFunctionName, "$singleCashFlow[$dateArgumentNameForCashFlow]", "$dateFromModalFrom", "$dateFromModalTo")) 
                {
                    if(array_key_exists($singleCashFlow[$categoryArgumentNameForCashFlow], $categoryKeyTotalAmountValue)) {
                        $categoryKeyTotalAmountValue[$singleCashFlow[$categoryArgumentNameForCashFlow]] = round((double)$categoryKeyTotalAmountValue[$singleCashFlow[$categoryArgumentNameForCashFlow]] + (double)$singleCashFlow['amount'],2);
                    }
                    else {
                        $categoryKeyTotalAmountValue[$singleCashFlow[$categoryArgumentNameForCashFlow]] = (double)$singleCashFlow['amount'];
                    }
                }
            }
        }
        else
        {
            throw new \Exception("Date for modal window incorrect");
        }

        return  $categoryKeyTotalAmountValue;
    }

    
    public static function getCategoryAndRelatedAmount($userId, $dateCheckFunctionName, $incomeOrExpense, $dateFromModalFrom="0", $dateFromModalTo="0") 
    {
        $dateArgumentNameForCashFlow = 'date_of_' . $incomeOrExpense;
        $categoryArgumentNameForCashFlow = $incomeOrExpense . '_category_assigned_to_user_id';
        $cashFlowPlural = $incomeOrExpense . 's'; 
    
        $sql = "SELECT amount, {$dateArgumentNameForCashFlow}, {$categoryArgumentNameForCashFlow} 
                FROM {$cashFlowPlural}
                WHERE user_id = :user_id";

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $categoryAndAmountsArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
     
        $categoryKeyTotalAmountValue = static::sumAmountsPerCategory($categoryAndAmountsArray, $dateCheckFunctionName, $incomeOrExpense, $dateFromModalFrom, $dateFromModalTo);
         
        $categoryTotalAmountValue = [];
     
    /*
        foreach($categoryAndAmountsArray as $singleExpense) 
        {
            if (Date::isCurrentMonthDate($singleExpense['date_of_expense'])) 
            {
                if(array_key_exists($singleExpense['expense_category_assigned_to_user_id'], $categoryKeyTotalAmountValue)) {
                    $categoryKeyTotalAmountValue[$singleExpense['expense_category_assigned_to_user_id']] = round((double)$categoryKeyTotalAmountValue[$singleExpense['expense_category_assigned_to_user_id']] + (double)$singleExpense['amount'],2);
                }
                else {
                    $categoryKeyTotalAmountValue[$singleExpense['expense_category_assigned_to_user_id']] = (double)$singleExpense['amount'];
                }
            }
        }
     */    


        foreach($categoryKeyTotalAmountValue as $key => $value) {
            $categoryTotalAmountValue[] = array($key, static::formatNumberInBudget($value));
        }

        return $categoryTotalAmountValue;
    } 

    /*
    public static function getCategoryAndRelatedAmountForIncome($userId, $dateCheckFunctionName) 
    {
        $sql = "SELECT amount, date_of_income, income_category_assigned_to_user_id 
                FROM incomes
                WHERE user_id = :user_id";

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $categoryAndAmountsArray = $stmt->fetchAll(PDO::FETCH_ASSOC);
     
        $categoryKeyTotalAmountValue = static::sumAmountsPerCategory($categoryAndAmountsArray, $dateCheckFunctionName, 'income');
        $categoryTotalAmountValue = [];
   */  
 /*       
        foreach($categoryAndAmountsArray as $singleIncome) 
        {
            if ($periodOfTime == 'CurrentMonth' && Date::isCurrentMonthDate($singleIncome['date_of_income'])) 
            {
                if(array_key_exists($singleIncome['income_category_assigned_to_user_id'], $categoryKeyTotalAmountValue)) {
                    $categoryKeyTotalAmountValue[$singleIncome['income_category_assigned_to_user_id']] = round((double)$categoryKeyTotalAmountValue[$singleIncome['income_category_assigned_to_user_id']] + (double)$singleIncome['amount'],2);
                }
                else {
                    $categoryKeyTotalAmountValue[$singleIncome['income_category_assigned_to_user_id']] = (double)$singleIncome['amount'];
                }
            }
            elseif ($periodOfTime == 'PreviousMonth' && Date::isPreviousMonthDate($singleIncome['date_of_income']))
            {

            }
            elseif ($periodOfTime == 'CurrentYear' && Date::isCurrentYearDate($singleIncome['date_of_income']))
            {

            }
        }
*/       
/*

        foreach($categoryKeyTotalAmountValue as $key => $value) {
            $categoryTotalAmountValue[] = array($key, static::formatNumberInBudget($value));
        }

        return $categoryTotalAmountValue;
    }
*/
    public static function getTotalExpense($userId, &$categoryAmountForExpenseSection) {
     //   $categoryAmountForExpenseSection = self::getCategoryAndRelatedAmount($dbConnection, $user_id, $class, $method, $startDateFromModal, $endDateFromModal);
        $totalExpense = 0;

        foreach ($categoryAmountForExpenseSection as $expensePerCategory) {
            $totalExpense = round($totalExpense + (double)$expensePerCategory[1], 2);
        }

        return  $totalExpense;

    }

    public static function getTotalIncome($userId, &$categoryAmountForIncomeSection) {
        //   $categoryAmountForExpenseSection = self::getCategoryAndRelatedAmount($dbConnection, $user_id, $class, $method, $startDateFromModal, $endDateFromModal);
           $totalIncome = 0;
   
           foreach ($categoryAmountForIncomeSection as $incomePerCategory) {
               $totalIncome = round($totalIncome + (double)$incomePerCategory[1], 2);
           }
   
           return  $totalIncome;
   
       }

    public static function getTotalBalance($totalIncome, $totalExpense) {
    //    $categoryAmountForExpenseSection = static::getCategoryAndRelatedAmountForExpense($userId);
    //    $categoryAmountForIncomeSection = static::getCategoryAndRelatedAmountForIncome($userId);
   //     $totalExpense = 0;
   //     $totalIncome = 0;
        $balance = 0;

    /*    foreach ($categoryAmountForExpenseSection as $expensePerCategory) {
            $totalExpense = round($totalExpense + (double)$expensePerCategory[1], 2);
        }

        foreach ($categoryAmountForIncomeSection as $incomePerCategory) {
            $totalIncome = round($totalIncome + (double)$incomePerCategory[1], 2);
        }
*/
  //      $totalExpense = static::getTotalExpense($userId, $categoryAmountForExpenseSection);
  //      $totalIncome = static::getTotalIncome($userId, $categoryAmountForIncomeSection);
        
        $balance = round($totalIncome - $totalExpense, 2);

        return static::formatNumberInBudget($balance);
       
    }

    public static function getAllItemsForExpenseOrIncome($userId, $dateCheckFunctionName, $incomeOrExpense, $dateFromModalFrom="0", $dateFromModalTo="0") {
        $dateArgumentNameForCashFlow = 'date_of_' . $incomeOrExpense;
        $categoryArgumentNameForCashFlow = $incomeOrExpense . '_category_assigned_to_user_id';
        $cashFlowComment = $incomeOrExpense . '_comment';
        $cashFlowPlural = $incomeOrExpense . 's'; 

        $sql = "SELECT *
                FROM {$cashFlowPlural}
                WHERE user_id = :user_id
                ORDER BY id";

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);        

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
       
        $expenseOrIncomeTable = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $expenseOrIncomeTableForGivenPeriod = [];
        $dateCheckFunctionName = '\App\Date::' . $dateCheckFunctionName;
              
        if ($dateFromModalFrom=="0" && $dateFromModalTo=="0") 
        {
            if ($incomeOrExpense == "expense") 
            {
                for ($i = 0; $i < count($expenseOrIncomeTable); $i++) 
                {
                    if (call_user_func($dateCheckFunctionName, $expenseOrIncomeTable[$i][$dateArgumentNameForCashFlow])) 
                    {
                        $expenseOrIncomeTableForGivenPeriod[] = array(
                                                            'id' => $expenseOrIncomeTable[$i]['id'], 
                                                            'date' => $expenseOrIncomeTable[$i][$dateArgumentNameForCashFlow],
                                                            'category' => $expenseOrIncomeTable[$i][$categoryArgumentNameForCashFlow],
                                                            'payment' => $expenseOrIncomeTable[$i]['payment_method_assigned_to_user_id'],
                                                            'comment' => $expenseOrIncomeTable[$i][$cashFlowComment],
                                                            'amount' => static::formatNumberInBudget($expenseOrIncomeTable[$i]['amount']));             
                    }
    
                }
            }

            elseif ($incomeOrExpense == "income") 
            {
                for ($i = 0; $i < count($expenseOrIncomeTable); $i++) 
                {
                    if (call_user_func($dateCheckFunctionName, $expenseOrIncomeTable[$i][$dateArgumentNameForCashFlow])) 
                    {
                        $expenseOrIncomeTableForGivenPeriod[] = array(
                                                            'id' => $expenseOrIncomeTable[$i]['id'], 
                                                            'date' => $expenseOrIncomeTable[$i][$dateArgumentNameForCashFlow],
                                                            'category' => $expenseOrIncomeTable[$i][$categoryArgumentNameForCashFlow],
                                                            'comment' => $expenseOrIncomeTable[$i][$cashFlowComment],
                                                            'amount' => static::formatNumberInBudget($expenseOrIncomeTable[$i]['amount']));             
                    }
    
                }
            }

        }
        elseif ($dateFromModalFrom!="0" && $dateFromModalTo!="0" && strtotime($dateFromModalFrom) && strtotime($dateFromModalTo)) 
        {
            if ($incomeOrExpense == "expense") 
            {
                for ($i = 0; $i < count($expenseOrIncomeTable); $i++) 
                {
                    if (call_user_func($dateCheckFunctionName, $expenseOrIncomeTable[$i][$dateArgumentNameForCashFlow], $dateFromModalFrom, $dateFromModalTo)) 
                    {
                        $expenseOrIncomeTableForGivenPeriod[] = array(
                                                            'id' => $expenseOrIncomeTable[$i]['id'], 
                                                            'date' => $expenseOrIncomeTable[$i][$dateArgumentNameForCashFlow],
                                                            'category' => $expenseOrIncomeTable[$i][$categoryArgumentNameForCashFlow],
                                                            'payment' => $expenseOrIncomeTable[$i]['payment_method_assigned_to_user_id'],
                                                            'comment' => $expenseOrIncomeTable[$i][$cashFlowComment],
                                                            'amount' => static::formatNumberInBudget($expenseOrIncomeTable[$i]['amount']));             
                    }
                }
            }

            elseif ($incomeOrExpense == "income") 
            {
                for ($i = 0; $i < count($expenseOrIncomeTable); $i++) 
                {
                    if (call_user_func($dateCheckFunctionName, $expenseOrIncomeTable[$i][$dateArgumentNameForCashFlow], $dateFromModalFrom, $dateFromModalTo)) 
                    {
                        $expenseOrIncomeTableForGivenPeriod[] = array(
                                                            'id' => $expenseOrIncomeTable[$i]['id'], 
                                                            'date' => $expenseOrIncomeTable[$i][$dateArgumentNameForCashFlow],
                                                            'category' => $expenseOrIncomeTable[$i][$categoryArgumentNameForCashFlow],
                                                            'comment' => $expenseOrIncomeTable[$i][$cashFlowComment],
                                                            'amount' => static::formatNumberInBudget($expenseOrIncomeTable[$i]['amount']));             
                    }
                }
            }

        }
        else
        {
            throw new \Exception("Date for modal window incorrect");  
        }

        return  $expenseOrIncomeTableForGivenPeriod;
    }

}
<?php

namespace App\Models;

use PDO;
use \App\Date;
use \App\Validation;
use \App\Models\User;


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
/*
    public static function validateCategory($category) 
    {
        $category = Validation::testInput($category);
        $errors = [];
        //Name
       if ($category == '') 
       {
           $errors[] = 'Category not provided';
       }
       elseif ($category != '') 
       {
           if (!preg_match("/^([a-zA-Z]+)* ?[a-zA-Z]+$/",$category) || strlen($category) > 20) 
           {
               $errors[] = "Only letters and one space allowed in name. Maximum number of characters is 20";
           }
       }

       return $errors;
    }
*/


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
        $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, date_of_income_first, income_comment)
        VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :date_of_income_first, :income_comment)';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':income_category_assigned_to_user_id', $category, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindValue(':date_of_income', $date, PDO::PARAM_STR);
        $stmt->bindValue(':date_of_income_first', $date, PDO::PARAM_STR);
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
        $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, date_of_expense_first, expense_comment)
        VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :date_of_expense_first, :expense_comment)';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':expense_category_assigned_to_user_id', $category, PDO::PARAM_STR);
        $stmt->bindValue(':payment_method_assigned_to_user_id', $payment, PDO::PARAM_STR);
        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindValue(':date_of_expense', $date, PDO::PARAM_STR);
        $stmt->bindValue(':date_of_expense_first', $date, PDO::PARAM_STR);
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

    public static function checkIfCategoryIsAssignedToUser($user_id, $name)
    {
        $sql = "SELECT *
                FROM expenses_category_assigned_to_users
                WHERE user_id = :user_id AND name = :name";

        $dbConnection = static::getDB();
        $stmt =  $dbConnection->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function addCategory($user_id, $categoryProvidedByUser)
    {
        $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name)
                VALUES (:user_id, :name)';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $categoryProvidedByUser, PDO::PARAM_STR);
                                        
        $stmt->execute();
        
        return $stmt->rowCount() ? true : false;
    }

    public static function countNumberOfCategories($user_id)
    {
        $sql = 'SELECT COUNT(*)
                FROM expenses_category_assigned_to_users
                WHERE user_id = :user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                                        
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_NUM);
    }

    public static function countNumberOfCategoriesInExpensesList($user_id)
    {
        $sql = 'SELECT COUNT(DISTINCT expense_category_assigned_to_user_id)
                FROM expenses
                WHERE user_id = :user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                                        
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_NUM);
    }

    public static function checkIfCategoryPresentOnExpensesList($user_id, $category)
    {
        $sql = 'SELECT COUNT(DISTINCT expense_category_assigned_to_user_id)
                FROM expenses
                WHERE user_id = :user_id AND expense_category_assigned_to_user_id = :expense_category_assigned_to_user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':expense_category_assigned_to_user_id', $category, PDO::PARAM_STR);
                                        
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_NUM);

        if (is_array($result))
        {
            return (int)$result[0] ? 1 : 0;
        }
        else
        {
            return false;
        }
    }

    public static function checkIfPaymentIsAssignedToUser($user_id, $name)
    {
        $sql = "SELECT *
                FROM payment_methods_assigned_to_users
                WHERE user_id = :user_id AND name = :name";

        $dbConnection = static::getDB();
        $stmt =  $dbConnection->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function countNumberOfPayments($user_id)
    {
        $sql = 'SELECT COUNT(*)
                FROM payment_methods_assigned_to_users
                WHERE user_id = :user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                                        
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_NUM);
    }

    public static function countNumberOfPaymentsInExpensesList($user_id)
    {
        $sql = 'SELECT COUNT(DISTINCT payment_method_assigned_to_user_id)
                FROM expenses
                WHERE user_id = :user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
                                        
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_NUM);
    }

    public static function addPayment($user_id, $paymentProvidedByUser)
    {
        $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name)
                VALUES (:user_id, :name)';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $paymentProvidedByUser, PDO::PARAM_STR);
                                        
        $stmt->execute();
        
        return $stmt->rowCount() ? true : false;
    }

    public static function checkIfPaymentPresentOnExpensesList($user_id, $payment)
    {
        $sql = 'SELECT COUNT(DISTINCT payment_method_assigned_to_user_id)
                FROM expenses
                WHERE user_id = :user_id AND payment_method_assigned_to_user_id = :payment_method_assigned_to_user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':payment_method_assigned_to_user_id', $payment, PDO::PARAM_STR);
                                        
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_NUM);

        if (is_array($result))
        {
            return (int)$result[0] ? 1 : 0;
        }
        else
        {
            return false;
        }
    }

    public static function returnCategoriesAssignedToUser($user_id)
    {
        $sql = "SELECT *
                FROM expenses_category_assigned_to_users
                WHERE user_id = :user_id";

        $dbConnection = static::getDB();
        $stmt =  $dbConnection->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function deleteCategory($user_id, $category)
    {
        $sql = "DELETE FROM expenses_category_assigned_to_users 
                WHERE user_id = :user_id AND name = :name";

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $category, PDO::PARAM_STR);
                                        
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }
    
    public static function returnPaymentsAssignedToUser($user_id)
    {
        $sql = "SELECT *
                FROM payment_methods_assigned_to_users
                WHERE user_id = :user_id";

        $dbConnection = static::getDB();
        $stmt =  $dbConnection->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function deletePayment($user_id, $payment)
    {
        $sql = "DELETE FROM payment_methods_assigned_to_users 
                WHERE user_id = :user_id AND name = :name";

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':name', $payment, PDO::PARAM_STR);
                                        
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

    public static function returnAllExpenses($user_id)
    {
        $sql = 'SELECT *
                FROM expenses
                WHERE user_id = :user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);                                
        $stmt->execute();
        $expenseArray = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($expenseArray as &$expense)
        {
            $expense['amount'] = static::formatNumberInBudget($expense['amount']);
        }

        return $expenseArray;
    }

    public static function returnExpensesId($user_id)
    {
        $sql = 'SELECT id
                FROM expenses
                WHERE user_id = :user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);                                
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function checkIfCurrentCategoryForExpenseItemIsTheSame($expense_id, $category)
    {
        $sql = 'SELECT COUNT(expense_category_assigned_to_user_id)
                FROM expenses
                WHERE id = :id AND expense_category_assigned_to_user_id = :expense_category_assigned_to_user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':expense_category_assigned_to_user_id', $category, PDO::PARAM_STR);                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_NUM);

        if (is_array($result))
        {
            return (int)$result[0] ? 1 : 0;
        }
        else
        {
            return false;
        }

    }

    public static function changeCategoryForExpenseItem($expense_id, $category)
    {
        $sql = 'UPDATE expenses
                SET expense_category_assigned_to_user_id = :expense_category_assigned_to_user_id
                WHERE id = :id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':expense_category_assigned_to_user_id', $category, PDO::PARAM_STR);                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

    public static function checkIfCurrentPaymentForExpenseItemIsTheSame($expense_id, $payment)
    {
        $sql = 'SELECT COUNT(payment_method_assigned_to_user_id)
                FROM expenses
                WHERE id = :id AND payment_method_assigned_to_user_id = :payment_method_assigned_to_user_id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':payment_method_assigned_to_user_id', $payment, PDO::PARAM_STR);                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_NUM);

        if (is_array($result))
        {
            return (int)$result[0] ? 1 : 0;
        }
        else
        {
            return false;
        }

    }

    public static function changePaymentForExpenseItem($expense_id, $payment)
    {
        $sql = 'UPDATE expenses
                SET payment_method_assigned_to_user_id = :payment_method_assigned_to_user_id
                WHERE id = :id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':payment_method_assigned_to_user_id', $payment, PDO::PARAM_STR);                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

    public static function checkIfCurrentAmountForExpenseItemIsTheSame($expense_id, $amount)
    {
        $sql = 'SELECT COUNT(amount)
                FROM expenses
                WHERE id = :id AND amount = :amount';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_NUM);

        if (is_array($result))
        {
            return (int)$result[0] ? 1 : 0;
        }
        else
        {
            return false;
        }

    }

    public static function changeAmountForExpenseItem($expense_id, $amount)
    {
        $sql = 'UPDATE expenses
                SET amount = :amount
                WHERE id = :id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

    public static function returnSingleExpenseItem($expense_id)
    {
        $sql = 'SELECT *
                FROM expenses
                WHERE id = :id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);   
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());                             
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public static function checkIfCurrentDateForExpenseItemIsTheSame($expense_id, $date)
    {
        $sql = 'SELECT COUNT(date_of_expense)
                FROM expenses
                WHERE id = :id AND date_of_expense = :date_of_expense';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':date_of_expense', $date, PDO::PARAM_STR);                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_NUM);

        if (is_array($result))
        {
            return (int)$result[0] ? 1 : 0;
        }
        else
        {
            return false;
        }

    }

    public static function changeDateForExpenseItem($expense_id, $date)
    {
        $sql = 'UPDATE expenses
                SET date_of_expense = :date_of_expense
                WHERE id = :id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':date_of_expense', $date, PDO::PARAM_STR);                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

    public static function changeCommentForExpenseItem($expense_id, $comment)
    {
        $sql = 'UPDATE expenses
                SET expense_comment = :expense_comment
                WHERE id = :id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                
        $stmt->bindValue(':expense_comment', $comment, PDO::PARAM_STR);                                
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

    public static function deleteExpenseItem($expense_id)
    {
        $sql = 'DELETE FROM expenses 
                WHERE id = :id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);
                                                                               
        $stmt->bindValue(':id', $expense_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() ? true : false;
    }

}
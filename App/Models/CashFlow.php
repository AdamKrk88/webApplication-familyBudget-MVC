<?php

namespace App\Models;

use PDO;


/**
 * Cash flow model - incomes, expenses, payment options
 *
 * PHP version 7.2.0
 */
class CashFlow extends \Core\Model
{

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
}
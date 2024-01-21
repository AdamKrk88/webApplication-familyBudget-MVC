<?php

namespace App\Controllers;

use \App\Models\User;
use \App\Models\CashFlow;

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
        if ((!isset($_POST['amount']) || !isset($_POST['date'])) && (!isset($_SESSION['amount']) || !isset($_SESSION['date'])))
        {
            throw new \Exception("Stop processing the request");
            // $this->redirect('/block/stopAjaxProcessing');
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

}
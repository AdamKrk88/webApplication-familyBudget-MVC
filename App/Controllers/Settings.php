<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;
use \App\Validation;
use \App\Models\CashFlow;

/**
 * Settings controller
 *
 * PHP version 7.2.0
 */
class Settings extends Authenticated
{

    private function addExpenseCategoryToDatabaseDirectly($categoryProvidedByUser)
    {
        $isAdded = CashFlow::addExpenseCategory($_SESSION['user_id'], $categoryProvidedByUser);
                        
        if ($isAdded)
        {
            Flash::addMessage('Category ' . '\'' . $categoryProvidedByUser . '\'' . ' added', Flash::ORANGE);
        }
        else
        {
            Flash::addMessage('Database error', Flash::ORANGE);
        }
    }

    private function addIncomeCategoryToDatabaseDirectly($categoryProvidedByUser)
    {
        $isAdded = CashFlow::addIncomeCategory($_SESSION['user_id'], $categoryProvidedByUser);
                        
        if ($isAdded)
        {
            Flash::addMessage('Category ' . '\'' . $categoryProvidedByUser . '\'' . ' added', Flash::ORANGE);
        }
        else
        {
            Flash::addMessage('Database error', Flash::ORANGE);
        }
    }

    private function addPaymentToDatabaseDirectly($paymentProvidedByUser)
    {
        $isAdded = CashFlow::addPayment($_SESSION['user_id'], $paymentProvidedByUser);
                        
        if ($isAdded)
        {
            Flash::addMessage('Payment method ' . '\'' . $paymentProvidedByUser . '\'' . ' added', Flash::ORANGE);
        }
        else
        {
            Flash::addMessage('Database error', Flash::ORANGE);
        }
    }


    public function displaySettingsOptionsAction()
    {   
        if (isset($this->route_params['option'])) 
        {
            $option = $this->route_params['option'];

            if ($option === 'user')
            {
                View::renderTemplate('Settings_user/user.html');
            }
            elseif ($option === 'expense')
            {
                View::renderTemplate('Settings_expense/expense.html');
            }
            elseif ($option === 'income')
            {
                View::renderTemplate('Settings_income/income.html');
            }
        }
        else
        {
            View::renderTemplate('Settings/options.html');  
        }  
    }


    public function displaySettingsUserAction()
    {   
        if (isset($this->route_params['option'])) 
        {
            $option = $this->route_params['option'];

            if ($option === 'change-name')
            {
                View::renderTemplate('Settings_user/user_change_name.html');
            }
            elseif ($option === 'change-email')
            {
                View::renderTemplate('Settings_user/user_change_email.html');
            }
            elseif ($option === 'change-password')
            {
                View::renderTemplate('Settings_user/user_change_password.html');
            }
            elseif ($option === 'profile')
            {
                $user = User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

                if ($user)
                {
                    View::renderTemplate('Settings_user/user_profile.html', [
                        'username' => $user->username,
                        'email' => $user->email  
                    ]);
                }
                else
                {
                    View::renderTemplate('Settings_user/user_profile.html');
                }
            }
        }
        else
        {
            View::renderTemplate('Settings/options.html');  
        }  
    }

    public function displaySettingsExpenseAction()
    {   
        if (isset($this->route_params['option'])) 
        {
            $option = $this->route_params['option'];

            if ($option === 'add-category')
            {
                View::renderTemplate('Settings_expense/expense_add_category.html');
            }
            elseif ($option === 'add-payment')
            {
                View::renderTemplate('Settings_expense/expense_add_payment.html');
            }
            elseif ($option === 'delete-category')
            {
                $expensesCategories = CashFlow::returnExpenseCategoriesAssignedToUser($_SESSION['user_id']);

                if (!empty($expensesCategories))
                {
                    View::renderTemplate('Settings_expense/expense_delete_category.html', [
                        'categories' => $expensesCategories
                    ]);
                }
                elseif (is_array($expensesCategories))
                {
                    View::renderTemplate('Settings_expense/expense_delete_category.html');
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_delete_category.html');
                }
            }
            elseif ($option === 'delete-payment')
            {
                $expensesPayments = CashFlow::returnPaymentsAssignedToUser($_SESSION['user_id']);

                if (!empty($expensesPayments))
                {
                    View::renderTemplate('Settings_expense/expense_delete_payment.html', [
                        'payments' => $expensesPayments
                    ]);
                }
                elseif (is_array($expensesPayments))
                {
                    View::renderTemplate('Settings_expense/expense_delete_payment.html');
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_delete_payment.html');
                }
            }
            elseif ($option === 'expense-list')
            {
                $expenseArray = CashFlow::returnAllExpenses($_SESSION['user_id']);
        
                if (!empty($expenseArray))
                {
                    View::renderTemplate('Settings_expense/expense_list.html', [
                        'expenses' => $expenseArray
                    ]);
                }
                elseif (is_array($expenseArray))
                {
                    View::renderTemplate('Settings_expense/expense_list.html');
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_list.html');
                }
            }
            elseif ($option === 'change-category')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expensesCategories = CashFlow::returnExpenseCategoriesAssignedToUser($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }
        
                if (is_array($expensesCategories) && is_array($expenseIdArray))
                {
               
                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                    //    $expenseNumber = $_SESSION['expenseNumber'];
                        $selected = $expenseNumber;
                  //      unset($_SESSION['expenseNumber']);
                    }

                    View::renderTemplate('Settings_expense/expense_change_category.html', [
                        'ids' => $expesneIdArrayLength,
                        'categories' => $expensesCategories,
                        'selected' => $selected
                    ]); 
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers and categories not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_category.html');
                }
            }
            elseif ($option === 'change-payment')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expensesPayments = CashFlow::returnPaymentsAssignedToUser($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }
        
                if (is_array($expensesPayments) && is_array($expenseIdArray))
                {
               
                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                        $selected = $expenseNumber;
                    }

                    View::renderTemplate('Settings_expense/expense_change_payment.html', [
                        'ids' => $expesneIdArrayLength,
                        'payments' => $expensesPayments,
                        'selected' => $selected
                    ]); 
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers and payments methods not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_payment.html');
                } 
            }
            elseif ($option === 'change-amount')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }

                if (is_array($expenseIdArray))
                {

                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                        $selected = $expenseNumber;
                    }

                    View::renderTemplate('Settings_expense/expense_change_amount.html', [
                        'ids' => $expesneIdArrayLength,
                        'selected' => $selected
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_amount.html');
                }

            }
            elseif ($option === 'change-date')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }

                if (is_array($expenseIdArray))
                {
                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                        $selected = $expenseNumber;
                    }

                    View::renderTemplate('Settings_expense/expense_change_date.html', [
                        'ids' => $expesneIdArrayLength,
                        'selected' => $selected
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_date.html');
                }
            }
            elseif ($option === 'change-comment')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;
                $comment = "";
                $commentValue = "";

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }

                if (isset($_SESSION['comment']))
                {
                    $comment = $_SESSION['comment'];
                    unset($_SESSION['comment']);
                }

                if (is_array($expenseIdArray))
                {
                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                        $selected = $expenseNumber;
                    }

                    if (!empty($_SESSION['flash_notifications']) && is_string($comment) && $comment !== "" && strlen($comment) > 0)
                    {
                        $commentValue = $comment;
                    }

                    View::renderTemplate('Settings_expense/expense_change_comment.html', [
                        'ids' => $expesneIdArrayLength,
                        'selected' => $selected,
                        'comment' => $commentValue
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_comment.html');
                }
            }
            elseif ($option === 'delete-item')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);

                if (is_array($expenseIdArray))
                {

                    View::renderTemplate('Settings_expense/expense_delete_item.html', [
                        'ids' => $expesneIdArrayLength
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_delete_item.html');
                }
            }
        }
        else
        {
            View::renderTemplate('Settings/options.html');  
        }  
    }


    public function displaySettingsIncomeAction()
    {   
        if (isset($this->route_params['option'])) 
        {
            $option = $this->route_params['option'];

            if ($option === 'add-category')
            {
                View::renderTemplate('Settings_income/income_add_category.html');
            }
            elseif ($option === 'delete-category')
            {
                $incomesCategories = CashFlow::returnIncomeCategoriesAssignedToUser($_SESSION['user_id']);

                if (!empty($incomesCategories))
                {
                    View::renderTemplate('Settings_income/income_delete_category.html', [
                        'categories' => $incomesCategories
                    ]);
                }
                elseif (is_array($incomesCategories))
                {
                    View::renderTemplate('Settings_income/income_delete_category.html');
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                    View::renderTemplate('Settings_income/income_delete_category.html');
                }
            }
            elseif ($option === 'income-list')
            {
                $incomeArray = CashFlow::returnAllIncomes($_SESSION['user_id']);
        
                if (!empty($incomeArray))
                {
                    View::renderTemplate('Settings_income/income_list.html', [
                        'incomes' => $incomeArray
                    ]);
                }
                elseif (is_array($incomeArray))
                {
                    View::renderTemplate('Settings_income/income_list.html');
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                    View::renderTemplate('Settings_income/income_list.html');
                }
            }
            elseif ($option === 'change-category')
            {
                $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);
                $incomesCategories = CashFlow::returnIncomeCategoriesAssignedToUser($_SESSION['user_id']);
                $incomeIdArrayLength = count($incomeIdArray);
                $selected = 0;
                $incomeNumber = 0;

                if (isset($_SESSION['incomeNumber']))
                {
                    $incomeNumber = (int)$_SESSION['incomeNumber'];
                    unset($_SESSION['incomeNumber']);
                }
        
                if (is_array($incomesCategories) && is_array($incomeIdArray))
                {
               
                    if (!empty($_SESSION['flash_notifications']) &&  $incomeNumber > 0)
                    {
                    //    $expenseNumber = $_SESSION['expenseNumber'];
                        $selected =  $incomeNumber;
                  //      unset($_SESSION['expenseNumber']);
                    }

                    View::renderTemplate('Settings_income/income_change_category.html', [
                        'ids' => $incomeIdArrayLength,
                        'categories' => $incomesCategories,
                        'selected' => $selected
                    ]); 
                }
                else
                {
                    Flash::addMessage('Database error - income numbers and categories not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_income/income_change_category.html');
                }
            }
            elseif ($option === 'change-amount')
            {
                $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);
                $incomeIdArrayLength = count($incomeIdArray);
                $selected = 0;
                $incomeNumber = 0;

                if (isset($_SESSION['incomeNumber']))
                {
                    $incomeNumber = (int)$_SESSION['incomeNumber'];
                    unset($_SESSION['incomeNumber']);
                }

                if (is_array($incomeIdArray))
                {

                    if (!empty($_SESSION['flash_notifications']) && $incomeNumber > 0)
                    {
                        $selected = $incomeNumber;
                    }

                    View::renderTemplate('Settings_income/income_change_amount.html', [
                        'ids' => $incomeIdArrayLength,
                        'selected' => $selected
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - income numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_income/income_change_amount.html');
                }

            }
            elseif ($option === 'change-date')
            {
                $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);
                $incomeIdArrayLength = count($incomeIdArray);
                $selected = 0;
                $incomeNumber = 0;

                if (isset($_SESSION['incomeNumber']))
                {
                    $incomeNumber = (int)$_SESSION['incomeNumber'];
                    unset($_SESSION['incomeNumber']);
                }

                if (is_array($incomeIdArray))
                {
                    if (!empty($_SESSION['flash_notifications']) && $incomeNumber > 0)
                    {
                        $selected = $incomeNumber;
                    }

                    View::renderTemplate('Settings_income/income_change_date.html', [
                        'ids' => $incomeIdArrayLength,
                        'selected' => $selected
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - income numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_income/income_change_date.html');
                }
            }
            elseif ($option === 'change-comment')
            {
                $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);
                $incomeIdArrayLength = count($incomeIdArray);
                $selected = 0;
                $incomeNumber = 0;
                $comment = "";
                $commentValue = "";

                if (isset($_SESSION['incomeNumber']))
                {
                    $incomeNumber = (int)$_SESSION['incomeNumber'];
                    unset($_SESSION['incomeNumber']);
                }

                if (isset($_SESSION['comment']))
                {
                    $comment = $_SESSION['comment'];
                    unset($_SESSION['comment']);
                }

                if (is_array($incomeIdArray))
                {
                    if (!empty($_SESSION['flash_notifications']) && $incomeNumber > 0)
                    {
                        $selected = $incomeNumber;
                    }

                    if (!empty($_SESSION['flash_notifications']) && is_string($comment) && $comment !== "" && strlen($comment) > 0)
                    {
                        $commentValue = $comment;
                    }

                    View::renderTemplate('Settings_income/income_change_comment.html', [
                        'ids' => $incomeIdArrayLength,
                        'selected' => $selected,
                        'comment' => $commentValue
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - income numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_income/income_change_comment.html');
                }
            }
            elseif ($option === 'delete-item')
            {
                $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);
                $incomeIdArrayLength = count($incomeIdArray);

                if (is_array($incomeIdArray))
                {

                    View::renderTemplate('Settings_income/income_delete_item.html', [
                        'ids' => $incomeIdArrayLength
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - income numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_income/income_delete_item.html');
                }
            }
        }
        else
        {
            View::renderTemplate('Settings/options.html');  
        }  
    }










/*

    public function displaySettingsAction()
    {   
        if (isset($this->route_params['option'])) 
        {
            $option = $this->route_params['option'];

            if ($option === 'user')
            {
                View::renderTemplate('Settings_user/user.html');
            }
            elseif ($option === 'expense')
            {
                View::renderTemplate('Settings_expense/expense.html');
            }
            elseif ($option === 'income')
            {
                View::renderTemplate('Settings_income/income.html');
            }
            elseif ($option === 'change-name')
            {
                View::renderTemplate('Settings_user/user_change_name.html');
            }
            elseif ($option === 'change-email')
            {
                View::renderTemplate('Settings_user/user_change_email.html');
            }
            elseif ($option === 'change-password')
            {
                View::renderTemplate('Settings_user/user_change_password.html');
            }
            elseif ($option === 'profile')
            {
                $user = User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

                if ($user)
                {
                    View::renderTemplate('Settings_user/user_profile.html', [
                        'username' => $user->username,
                        'email' => $user->email  
                    ]);
                }
                else
                {
                    View::renderTemplate('Settings_user/user_profile.html');
                }
            }
            elseif ($option === 'add-category')
            {
                View::renderTemplate('Settings_expense/expense_add_category.html');
            }
            elseif ($option === 'add-payment')
            {
                View::renderTemplate('Settings_expense/expense_add_payment.html');
            }
            elseif ($option === 'delete-category')
            {
                $expensesCategories = CashFlow::returnCategoriesAssignedToUser($_SESSION['user_id']);

                if (!empty($expensesCategories))
                {
                    View::renderTemplate('Settings_expense/expense_delete_category.html', [
                        'categories' => $expensesCategories
                    ]);
                }
                elseif (is_array($expensesCategories))
                {
                    View::renderTemplate('Settings_expense/expense_delete_category.html');
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_delete_category.html');
                }
            }
            elseif ($option === 'delete-payment')
            {
                $expensesPayments = CashFlow::returnPaymentsAssignedToUser($_SESSION['user_id']);

                if (!empty($expensesPayments))
                {
                    View::renderTemplate('Settings_expense/expense_delete_payment.html', [
                        'payments' => $expensesPayments
                    ]);
                }
                elseif (is_array($expensesPayments))
                {
                    View::renderTemplate('Settings_expense/expense_delete_payment.html');
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_delete_payment.html');
                }
            }
            elseif ($option === 'expense-list')
            {
                $expenseArray = CashFlow::returnAllExpenses($_SESSION['user_id']);
           //     $expenseArray = false;
                if (!empty($expenseArray))
                {
                    View::renderTemplate('Settings_expense/expense_list.html', [
                        'expenses' => $expenseArray
                    ]);
                }
                elseif (is_array($expenseArray))
                {
                    View::renderTemplate('Settings_expense/expense_list.html');
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_list.html');
                }
            }
            elseif ($option === 'change-category')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expensesCategories = CashFlow::returnCategoriesAssignedToUser($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }
            //    $expensesCategories = false;
                if (is_array($expensesCategories) && is_array($expenseIdArray))
                {
               
                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                    //    $expenseNumber = $_SESSION['expenseNumber'];
                        $selected = $expenseNumber;
                  //      unset($_SESSION['expenseNumber']);
                    }

                    View::renderTemplate('Settings_expense/expense_change_category.html', [
                        'ids' => $expesneIdArrayLength,
                        'categories' => $expensesCategories,
                        'selected' => $selected
                    ]); 
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers and categories not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_category.html');
                }
            }
            elseif ($option === 'change-payment')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expensesPayments = CashFlow::returnPaymentsAssignedToUser($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }
        
                if (is_array($expensesPayments) && is_array($expenseIdArray))
                {
               
                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                        $selected = $expenseNumber;
                    }

                    View::renderTemplate('Settings_expense/expense_change_payment.html', [
                        'ids' => $expesneIdArrayLength,
                        'payments' => $expensesPayments,
                        'selected' => $selected
                    ]); 
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers and payments methods not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_payment.html');
                } 
            }
            elseif ($option === 'change-amount')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }

                if (is_array($expenseIdArray))
                {

                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                        $selected = $expenseNumber;
                    }

                    View::renderTemplate('Settings_expense/expense_change_amount.html', [
                        'ids' => $expesneIdArrayLength,
                        'selected' => $selected
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_amount.html');
                }

            }
            elseif ($option === 'change-date')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }

                if (is_array($expenseIdArray))
                {
                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                        $selected = $expenseNumber;
                    }

                    View::renderTemplate('Settings_expense/expense_change_date.html', [
                        'ids' => $expesneIdArrayLength,
                        'selected' => $selected
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_date.html');
                }
            }
            elseif ($option === 'change-comment')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);
                $selected = 0;
                $expenseNumber = 0;
                $comment = "";
                $commentValue = "";

                if (isset($_SESSION['expenseNumber']))
                {
                    $expenseNumber = (int)$_SESSION['expenseNumber'];
                    unset($_SESSION['expenseNumber']);
                }

                if (isset($_SESSION['comment']))
                {
                    $comment = $_SESSION['comment'];
                    unset($_SESSION['comment']);
                }

                if (is_array($expenseIdArray))
                {
                    if (!empty($_SESSION['flash_notifications']) && $expenseNumber > 0)
                    {
                        $selected = $expenseNumber;
                    }

                    if (!empty($_SESSION['flash_notifications']) && is_string($comment) && $comment !== "" && strlen($comment) > 0)
                    {
                        $commentValue = $comment;
                    }

                    View::renderTemplate('Settings_expense/expense_change_comment.html', [
                        'ids' => $expesneIdArrayLength,
                        'selected' => $selected,
                        'comment' => $commentValue
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_change_comment.html');
                }
            }
            elseif ($option === 'delete-item')
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expesneIdArrayLength = count($expenseIdArray);

                if (is_array($expenseIdArray))
                {

                    View::renderTemplate('Settings_expense/expense_delete_item.html', [
                        'ids' => $expesneIdArrayLength
                    ]);
                }
                else
                {
                    Flash::addMessage('Database error - expense numbers not generated', Flash::ORANGE);
                    View::renderTemplate('Settings_expense/expense_delete_item.html');
                }
            }
        }
        else
        {
            View::renderTemplate('Settings/options.html');  
        }  
    }

*/

    public function changeUsernameAction() 
    {
        $user = User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

        $user->username = $_POST['new-username'];

        $user->validateUsername();

     //   var_dump($user->errors);

        if (empty($user->errors))
        {
            if ($user->changeUsername())
            {
                Flash::addMessage('Username changed successfully', Flash::ORANGE);
            }
            else
            {
                Flash::addMessage('Database error', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage($user->errors[0], Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/user/change-name');

    }

    public function changeEmailAction() 
    {
        $user = User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

        $user->email_change = $_POST['new-email'];

        $user->validateNewEmail();

     //   var_dump($user->errors);

        if (empty($user->errors))
        {
            if($user->startEmailChange())
            {
                $user->sendEmailChangeInformationToUserNewMailbox();
                Flash::addMessage('Please check your new email and confirm the change', Flash::ORANGE);
            }
            else
            {
                Flash::addMessage('Database error', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage($user->errors[0], Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/user/change-email');

    }

    public function activateNewEmail()
    {
        $token = $this->route_params['token'];
        $user = User::getUserNewEmail($token);  

        if ($user) 
        {  
            if ($user->updateEmail($token))
            {
                Flash::addMessage('Email changed');
            }
            else
            {
                Flash::addMessage('Database error. Email not changed');
            }  
        }        
        else 
        {
            Flash::addMessage('Match with database record not found');
        }

     
        User::clearEmailChangeToken($token);
        
        View::renderTemplate('Settings_user/email_change_website.html');   

   //     Flash::addMessage('Match with database record not found');
   //     View::renderTemplate('Settings/email_change_website.html');
    }

    public function changePasswordAction()
    {
        $user = User::checkIfUserIdExistInDatabase($_SESSION['user_id']);

        $user->new_password = $_POST['new-password'];

        $user->validateNewPassword();

     //   var_dump($user->errors);

        if (empty($user->errors))
        {
            if ($user->checkIfPasswordIsDifferent())
            {
                if ($user->changePasswordByLoggedInUser())
                {
                    Flash::addMessage('Password changed successfully', Flash::ORANGE);
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                }
            }
            else
            {
                Flash::addMessage('No change. This is your current password', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage($user->errors[0], Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/user/change-password');
    }

    public function addExpenseCategoryAction() 
    {
        $categoryProvidedByUser = Validation::testInput($_POST['new-category']);
        $errors = Validation::validateCategoryOrPayment($categoryProvidedByUser, "Category");

        if (empty($errors))
        {
            $categoryProvidedByUser = Validation::capitalizeFirstLetter($categoryProvidedByUser);
            $expenseCategoryInDatabase = CashFlow::checkIfExpenseCategoryIsAssignedToUser($_SESSION['user_id'], $categoryProvidedByUser);

            if ($expenseCategoryInDatabase)
            {
                Flash::addMessage('Category exists. No changes done', Flash::ORANGE);
            }
            else
            {
                $numberOfCategories = CashFlow::countNumberOfExpenseCategories($_SESSION['user_id']);

                if ($numberOfCategories === false)
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                }
                else
                {
                    $numberOfCategories = (int)$numberOfCategories[0];

                    if ($numberOfCategories < 18)
                    {
                        $numberOfCategoriesInExpensesList = CashFlow::countNumberOfCategoriesInExpensesList($_SESSION['user_id']);
                        
                        if ($numberOfCategoriesInExpensesList === false)
                        {
                            Flash::addMessage('Database error', Flash::ORANGE);
                        }
                        else
                        {
                            $numberOfCategoriesInExpensesList = (int)$numberOfCategoriesInExpensesList[0];
                            
                            if ($numberOfCategoriesInExpensesList < 18)
                            {
                              $this->addExpenseCategoryToDatabaseDirectly($categoryProvidedByUser);
                            }
                            else
                            {
                                $isPresentOnExpensesList = CashFlow::checkIfCategoryPresentOnExpensesList($_SESSION['user_id'], $categoryProvidedByUser);

                                if ($isPresentOnExpensesList)
                                {
                                    $this->addExpenseCategoryToDatabaseDirectly($categoryProvidedByUser);
                                }
                                elseif ($isPresentOnExpensesList === 0)
                                {
                                    Flash::addMessage('Error. Your expenses list has maximum number of 18 different categories', Flash::ORANGE);
                                }
                                else
                                {
                                    Flash::addMessage('Database error', Flash::ORANGE);
                                }
                            }
                        }
                    }
                    else
                    {
                        Flash::addMessage('Error. Maximum number of categories is 18', Flash::ORANGE);
                    }
                }
            }
        }
        else
        {
            Flash::addMessage($errors[0], Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/expense/add-category');

    }

    public function addIncomeCategoryAction() 
    {
        $categoryProvidedByUser = Validation::testInput($_POST['new-category']);
        $errors = Validation::validateCategoryOrPayment($categoryProvidedByUser, "Category");

        if (empty($errors))
        {
            $categoryProvidedByUser = Validation::capitalizeFirstLetter($categoryProvidedByUser);
            $incomeCategoryInDatabase = CashFlow::checkIfIncomeCategoryIsAssignedToUser($_SESSION['user_id'], $categoryProvidedByUser);

            if ($incomeCategoryInDatabase)
            {
                Flash::addMessage('Category exists. No changes done', Flash::ORANGE);
            }
            else
            {
                $numberOfCategories = CashFlow::countNumberOfIncomeCategories($_SESSION['user_id']);

                if ($numberOfCategories === false)
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                }
                else
                {
                    $numberOfCategories = (int)$numberOfCategories[0];

                    if ($numberOfCategories < 18)
                    {
                        $numberOfCategoriesInIncomesList = CashFlow::countNumberOfCategoriesInIncomesList($_SESSION['user_id']);
                        
                        if ($numberOfCategoriesInIncomesList === false)
                        {
                            Flash::addMessage('Database error', Flash::ORANGE);
                        }
                        else
                        {
                            $numberOfCategoriesInIncomesList = (int)$numberOfCategoriesInIncomesList[0];
                            
                            if ($numberOfCategoriesInIncomesList < 18)
                            {
                              $this->addIncomeCategoryToDatabaseDirectly($categoryProvidedByUser);
                            }
                            else
                            {
                                $isPresentOnIncomesList = CashFlow::checkIfCategoryPresentOnIncomesList($_SESSION['user_id'], $categoryProvidedByUser);

                                if ($isPresentOnIncomesList)
                                {
                                    $this->addIncomeCategoryToDatabaseDirectly($categoryProvidedByUser);
                                }
                                elseif ($isPresentOnIncomesList === 0)
                                {
                                    Flash::addMessage('Error. Your incomes list has maximum number of 18 different categories', Flash::ORANGE);
                                }
                                else
                                {
                                    Flash::addMessage('Database error', Flash::ORANGE);
                                }
                            }
                        }
                    }
                    else
                    {
                        Flash::addMessage('Error. Maximum number of categories is 18', Flash::ORANGE);
                    }
                }
            }
        }
        else
        {
            Flash::addMessage($errors[0], Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/income/add-category');

    }

    public function addPaymentAction() 
    {
        $paymentProvidedByUser = Validation::testInput($_POST['new-payment']);
        $errors = Validation::validateCategoryOrPayment($paymentProvidedByUser, "Payment");

        if (empty($errors))
        {
            $paymentProvidedByUser = Validation::capitalizeFirstLetter($paymentProvidedByUser);
            $paymentInDatabase = CashFlow::checkIfPaymentIsAssignedToUser($_SESSION['user_id'], $paymentProvidedByUser);

            if ($paymentInDatabase)
            {
                Flash::addMessage('Payment method exists. No changes done', Flash::ORANGE);
            }
            else
            {
                $numberOfPayments = CashFlow::countNumberOfPayments($_SESSION['user_id']);

                if ($numberOfPayments === false)
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                }
                else
                {
                    $numberOfPayments = (int)$numberOfPayments[0];

                    if ($numberOfPayments < 5)
                    {
                        $numberOfPaymentsInExpensesList = CashFlow::countNumberOfPaymentsInExpensesList($_SESSION['user_id']);
                        
                        if ($numberOfPaymentsInExpensesList === false)
                        {
                            Flash::addMessage('Database error', Flash::ORANGE);
                        }
                        else
                        {
                            $numberOfPaymentsInExpensesList = (int)$numberOfPaymentsInExpensesList[0];
                            
                            if ($numberOfPaymentsInExpensesList < 5)
                            {
                              $this->addPaymentToDatabaseDirectly($paymentProvidedByUser);
                            }
                            else
                            {
                                $isPresentOnExpensesList = CashFlow::checkIfPaymentPresentOnExpensesList($_SESSION['user_id'], $paymentProvidedByUser);

                                if ($isPresentOnExpensesList)
                                {
                                    $this->addPaymentToDatabaseDirectly($paymentProvidedByUser);
                                }
                                elseif ($isPresentOnExpensesList === 0)
                                {
                                    Flash::addMessage('Error. Your expenses list has maximum number of 5 different payment methods', Flash::ORANGE);
                                }
                                else
                                {
                                    Flash::addMessage('Database error', Flash::ORANGE);
                                }
                            }
                        }
                    }
                    else
                    {
                        Flash::addMessage('Error. Maximum number of payment methods is 5', Flash::ORANGE);
                    }
                }
            }
        }
        else
        {
            Flash::addMessage($errors[0], Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/expense/add-payment');

    }

    public function deleteExpenseCategoryAction()
    {
        if (isset($_POST['category']))
        {
            $category = $_POST['category'];
            $category = Validation::testInput($category);
            $category = preg_replace('/\\\\u([\da-fA-F]{4})/', '&#x\1;', $category);
            $category = html_entity_decode($category);
            $errors = Validation::validateCategoryOrPayment($category, "Category");

            if(empty($errors))
            {
                $deletionResult = CashFlow::deleteExpenseCategory($_SESSION['user_id'], $category);

                if ($deletionResult)
                {
                    Flash::addMessage('Category ' . '\'' . $category . '\'' . ' deleted', Flash::ORANGE);
                }
                else
                {
                    Flash::addMessage('Error. Category ' . '\'' . $category . '\'' . ' not deleted', Flash::ORANGE);
                }
            }
            else
            {
                Flash::addMessage($errors[0], Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage('Form not submitted', Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/expense/delete-category');

    }

    public function deleteIncomeCategoryAction()
    {
        if (isset($_POST['category']))
        {
            $category = $_POST['category'];
            $category = Validation::testInput($category);
            $category = preg_replace('/\\\\u([\da-fA-F]{4})/', '&#x\1;', $category);
            $category = html_entity_decode($category);
            $errors = Validation::validateCategoryOrPayment($category, "Category");

            if(empty($errors))
            {
                $deletionResult = CashFlow::deleteIncomeCategory($_SESSION['user_id'], $category);

                if ($deletionResult)
                {
                    Flash::addMessage('Category ' . '\'' . $category . '\'' . ' deleted', Flash::ORANGE);
                }
                else
                {
                    Flash::addMessage('Error. Category ' . '\'' . $category . '\'' . ' not deleted', Flash::ORANGE);
                }
            }
            else
            {
                Flash::addMessage($errors[0], Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage('Form not submitted', Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/income/delete-category');

    }

    public function deletePaymentAction()
    {
        if (isset($_POST['payment']))
        {

            $payment = $_POST['payment'];
            $payment = Validation::testInput($payment);
            $payment = preg_replace('/\\\\u([\da-fA-F]{4})/', '&#x\1;', $payment);
            $payment = html_entity_decode($payment);
            $errors = Validation::validateCategoryOrPayment($payment, "Payment");

            if(empty($errors))
            {
                $deletionResult = CashFlow::deletePayment($_SESSION['user_id'], $payment);

                if ($deletionResult)
                {
                    Flash::addMessage('Payment method ' . '\'' . $payment . '\'' . ' deleted', Flash::ORANGE);
                }
                else
                {
                    Flash::addMessage('Error. Payment method ' . '\'' . $payment . '\'' . ' not deleted', Flash::ORANGE);
                }
            }   
            else
            {
                Flash::addMessage($errors[0], Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage('Form not submitted', Flash::ORANGE);
        }

        $this->redirect('/settings/display-settings/expense/delete-payment');

    }

    public function changeCategoryForExpenseItemAction()
    {
        if (!empty($_POST['expenseNumber']) && !empty($_POST['category']))
        {
            $expenseNumber = $_POST['expenseNumber'];
            $category = $_POST['category'];

            if ($expenseNumber != "--" && $category != "--")
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expenseId = $expenseIdArray[(int)$expenseNumber - 1]['id'];
                $expenseId = (int)$expenseId;
                $isCategoryTheSame = CashFlow::checkIfCurrentCategoryForExpenseItemIsTheSame($expenseId, $category);

                if ($isCategoryTheSame)
                {
                    Flash::addMessage('No changes. It is the current category for expense item ' . $expenseNumber , Flash::ORANGE);
                    $_SESSION['expenseNumber'] = $expenseNumber;
                }
                elseif ($isCategoryTheSame === 0)
                {
                    $isCategoryChanged = CashFlow::changeCategoryForExpenseItem($expenseId, $category);

                    if ($isCategoryChanged)
                    {
                        Flash::addMessage('Category ' . '\'' . $category . '\'' . ' assigned to item ' . $expenseNumber, Flash::ORANGE);
                    }
                    else
                    {
                        Flash::addMessage('Error. Category not changed', Flash::ORANGE);
                    }
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                }
                
            }
            else
            {
                Flash::addMessage('Error. Expense item or category cannot have value \'--\'', Flash::ORANGE);
            }
        
        }
        else
        {
            Flash::addMessage('Error. No value for expense item or category', Flash::ORANGE);
        }
         
        $this->redirect('/settings/display-settings/expense/change-category');
        
    }

    public function changeCategoryForIncomeItemAction()
    {
        if (!empty($_POST['incomeNumber']) && !empty($_POST['category']))
        {
            $incomeNumber = $_POST['incomeNumber'];
            $category = $_POST['category'];

            if ($incomeNumber != "--" && $category != "--")
            {
                $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);
                $incomeId = $incomeIdArray[(int)$incomeNumber - 1]['id'];
                $incomeId = (int)$incomeId;
                $isCategoryTheSame = CashFlow::checkIfCurrentCategoryForIncomeItemIsTheSame($incomeId, $category);

                if ($isCategoryTheSame)
                {
                    Flash::addMessage('No changes. It is the current category for income item ' . $incomeNumber , Flash::ORANGE);
                    $_SESSION['incomeNumber'] = $incomeNumber;
                }
                elseif ($isCategoryTheSame === 0)
                {
                    $isCategoryChanged = CashFlow::changeCategoryForIncomeItem($incomeId, $category);

                    if ($isCategoryChanged)
                    {
                        Flash::addMessage('Category ' . '\'' . $category . '\'' . ' assigned to item ' . $incomeNumber, Flash::ORANGE);
                    }
                    else
                    {
                        Flash::addMessage('Error. Category not changed', Flash::ORANGE);
                    }
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                }
                
            }
            else
            {
                Flash::addMessage('Error. Income item or category cannot have value \'--\'', Flash::ORANGE);
            }
        
        }
        else
        {
            Flash::addMessage('Error. No value for income item or category', Flash::ORANGE);
        }
         
        $this->redirect('/settings/display-settings/income/change-category');
        
    }

    public function changePaymentForExpenseItemAction()
    {
        if (!empty($_POST['expenseNumber']) && !empty($_POST['payment']))
        {
            $expenseNumber = $_POST['expenseNumber'];
            $payment = $_POST['payment'];

            if ($expenseNumber != "--" && $payment != "--")
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                $expenseId = $expenseIdArray[(int)$expenseNumber - 1]['id'];
                $expenseId = (int)$expenseId;
                $isPaymentTheSame = CashFlow::checkIfCurrentPaymentForExpenseItemIsTheSame($expenseId, $payment);

                if ($isPaymentTheSame)
                {
                    Flash::addMessage('No changes. It is the current payment method for expense item ' . $expenseNumber , Flash::ORANGE);
                    $_SESSION['expenseNumber'] = $expenseNumber;
                }
                elseif ($isPaymentTheSame === 0)
                {
                    $isPaymentChanged = CashFlow::changePaymentForExpenseItem($expenseId, $payment);

                    if ($isPaymentChanged)
                    {
                        Flash::addMessage('Payment method ' . '\'' . $payment . '\'' . ' assigned to item ' . $expenseNumber, Flash::ORANGE);
                    }
                    else
                    {
                        Flash::addMessage('Error. Payment method not changed', Flash::ORANGE);
                    }
                }
                else
                {
                    Flash::addMessage('Database error', Flash::ORANGE);
                }
                
            }
            else
            {
                Flash::addMessage('Error. Expense item or payment method cannot have value \'--\'', Flash::ORANGE);
            }
        
        }
        else
        {
            Flash::addMessage('Error. No value for expense item or payment method', Flash::ORANGE);
        }
         
        $this->redirect('/settings/display-settings/expense/change-payment');
        
    }

    public function changeAmountForExpenseItemAction()
    {
        if (!empty($_POST['expenseNumber']) && !empty($_POST['amount']))
        {
            $expenseNumber = $_POST['expenseNumber'];
            $amount = $_POST['amount'];
            $valueNotAllowed = array("--","0.", "0.0", "0.00");

            if (!in_array($expenseNumber, $valueNotAllowed) && !in_array($amount, $valueNotAllowed))
            {
                $amount = Validation::testInput($amount);
                $errors = Validation::validateAmount($amount);
                
                if (empty($errors))
                {
                    $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);
                    $expenseId = $expenseIdArray[(int)$expenseNumber - 1]['id'];
                    $expenseId = (int)$expenseId;
                    $isAmountTheSame = CashFlow::checkIfCurrentAmountForExpenseItemIsTheSame($expenseId, $amount);

                    if ($isAmountTheSame)
                    {
                        Flash::addMessage('No changes. It is the current amount for expense item ' . $expenseNumber , Flash::ORANGE);
                        $_SESSION['expenseNumber'] = $expenseNumber;
                    }
                    elseif ($isAmountTheSame === 0)
                    {
                        $isAmountChanged = CashFlow::changeAmountForExpenseItem($expenseId, $amount);
    
                        if ($isAmountChanged)
                        {
                            Flash::addMessage('Amount ' . '\'' . $amount . '\'' . ' assigned to item ' . $expenseNumber, Flash::ORANGE);
                        }
                        else
                        {
                            Flash::addMessage('Error. Amount not changed', Flash::ORANGE);
                        }
                        
                    }
                    else
                    {
                        Flash::addMessage('Database error', Flash::ORANGE);
                    }
                }
                else
                {
                    Flash::addMessage($errors[0], Flash::ORANGE);
                }
            }
            else
            {
                Flash::addMessage('Error. Expense item or amount cannot have value \'--\' or value 0', Flash::ORANGE);
            }
        }         
        else
        {
            Flash::addMessage('Error. No value for expense item or amount', Flash::ORANGE);
        }  

        $this->redirect('/settings/display-settings/expense/change-amount');
        
    }

    public function changeAmountForIncomeItemAction()
    {
        if (!empty($_POST['incomeNumber']) && !empty($_POST['amount']))
        {
            $incomeNumber = $_POST['incomeNumber'];
            $amount = $_POST['amount'];
            $valueNotAllowed = array("--","0.", "0.0", "0.00");

            if (!in_array($incomeNumber, $valueNotAllowed) && !in_array($amount, $valueNotAllowed))
            {
                $amount = Validation::testInput($amount);
                $errors = Validation::validateAmount($amount);
                
                if (empty($errors))
                {
                    $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);
                    $incomeId = $incomeIdArray[(int)$incomeNumber - 1]['id'];
                    $incomeId = (int)$incomeId;
                    $isAmountTheSame = CashFlow::checkIfCurrentAmountForIncomeItemIsTheSame($incomeId, $amount);

                    if ($isAmountTheSame)
                    {
                        Flash::addMessage('No changes. It is the current amount for income item ' . $incomeNumber , Flash::ORANGE);
                        $_SESSION['incomeNumber'] = $incomeNumber;
                    }
                    elseif ($isAmountTheSame === 0)
                    {
                        $isAmountChanged = CashFlow::changeAmountForIncomeItem($incomeId, $amount);
    
                        if ($isAmountChanged)
                        {
                            Flash::addMessage('Amount ' . '\'' . $amount . '\'' . ' assigned to item ' . $incomeNumber, Flash::ORANGE);
                        }
                        else
                        {
                            Flash::addMessage('Error. Amount not changed', Flash::ORANGE);
                        }
                        
                    }
                    else
                    {
                        Flash::addMessage('Database error', Flash::ORANGE);
                    }
                }
                else
                {
                    Flash::addMessage($errors[0], Flash::ORANGE);
                }
            }
            else
            {
                Flash::addMessage('Error. Income item or amount cannot have value \'--\' or value 0', Flash::ORANGE);
            }
        }         
        else
        {
            Flash::addMessage('Error. No value for income item or amount', Flash::ORANGE);
        }  

        $this->redirect('/settings/display-settings/income/change-amount');
        
    }

    public function changeDateForExpenseItemAction()
    {
        if (!empty($_POST['expenseNumber']) && !empty($_POST['date']))
        {
            $expenseNumber = $_POST['expenseNumber'];
            $date = $_POST['date'];
            $isMatched = preg_match("/^[1-9]+\d*$/",$expenseNumber) ? true : false;

            if ($isMatched)
            {
                $date = Validation::testInput($date);
                $errors = Validation::validateDate($date);

                if (empty($errors))
                {
                    list($year, $month, $day) = explode('-', $date);
                    $isDate = checkdate($month, $day, $year);

                    if ($isDate)
                    {
                        $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);

                        if (is_array($expenseIdArray) && !empty($expenseIdArray))
                        {
                            $expenseId = $expenseIdArray[(int)$expenseNumber - 1]['id'];
                            $expenseId = (int)$expenseId;

                            $isDateTheSame = CashFlow::checkIfCurrentDateForExpenseItemIsTheSame($expenseId, $date);

                            if ($isDateTheSame)
                            {
                                Flash::addMessage('No changes. Date ' . $date . ' is the current one for expense item ' . $expenseNumber , Flash::ORANGE);
                                $_SESSION['expenseNumber'] = $expenseNumber;
                            }
                            elseif ($isDateTheSame === 0)
                            {
                                $singleExpenseItem = CashFlow::returnSingleExpenseItem($expenseId);

                                if ($date <= $singleExpenseItem->date_of_expense_current && $date >= '2023-01-01')
                                {
                                    $isDateChanged = CashFlow::changeDateForExpenseItem($expenseId, $date);
            
                                    if ($isDateChanged)
                                    {
                                        Flash::addMessage('Date ' . '\'' . $date . '\'' . ' assigned to item ' . $expenseNumber, Flash::ORANGE);
                                    }
                                    else
                                    {
                                        Flash::addMessage('Error. Date not changed', Flash::ORANGE);
                                    }  
                                }
                                else
                                {  
                                    if ($singleExpenseItem->date_of_expense === $singleExpenseItem->date_of_expense_current) 
                                    {
                                        $previousDate = date('Y-m-d', strtotime($singleExpenseItem->date_of_expense_current .' -1 day'));
                                        Flash::addMessage('You can use date from 2023-01-01 to ' . $previousDate . ' inclusive', Flash::ORANGE);
                                    }
                                    else
                                    {
                                        Flash::addMessage('You can use date from 2023-01-01 to ' . $singleExpenseItem->date_of_expense_current . ' inclusive. You cannot use date ' . $singleExpenseItem->date_of_expense, Flash::ORANGE);
                                    }
                                   
                                    $_SESSION['expenseNumber'] = $expenseNumber;
                                }
                                
                            }
                            else
                            {
                                Flash::addMessage('Database error', Flash::ORANGE);
                            }
                        }
                        else
                        {
                            Flash::addMessage('Database error or no expenses items registered', Flash::ORANGE);
                        }
                    }
                    else
                    {
                        Flash::addMessage('Error. Incorrect date provided - this date does not exist', Flash::ORANGE);
                    }
                }
                else
                {
                    Flash::addMessage($errors[0], Flash::ORANGE);
                }
            }
            else
            {
                Flash::addMessage('Error. Expense item should have numeric format', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage('Error. No value for expense item or date', Flash::ORANGE);
        }  

        $this->redirect('/settings/display-settings/expense/change-date');
    }

    public function changeDateForIncomeItemAction()
    {
        if (!empty($_POST['incomeNumber']) && !empty($_POST['date']))
        {
            $incomeNumber = $_POST['incomeNumber'];
            $date = $_POST['date'];
            $isMatched = preg_match("/^[1-9]+\d*$/",$incomeNumber) ? true : false;

            if ($isMatched)
            {
                $date = Validation::testInput($date);
                $errors = Validation::validateDate($date);

                if (empty($errors))
                {
                    list($year, $month, $day) = explode('-', $date);
                    $isDate = checkdate($month, $day, $year);

                    if ($isDate)
                    {
                        $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);

                        if (is_array($incomeIdArray) && !empty($incomeIdArray))
                        {
                            $incomeId = $incomeIdArray[(int)$incomeNumber - 1]['id'];
                            $incomeId = (int)$incomeId;

                            $isDateTheSame = CashFlow::checkIfCurrentDateForIncomeItemIsTheSame($incomeId, $date);

                            if ($isDateTheSame)
                            {
                                Flash::addMessage('No changes. Date ' . $date . ' is the current one for income item ' . $incomeNumber , Flash::ORANGE);
                                $_SESSION['incomeNumber'] = $incomeNumber;
                            }
                            elseif ($isDateTheSame === 0)
                            {
                                $singleIncomeItem = CashFlow::returnSingleIncomeItem($incomeId);

                                if ($date <= $singleIncomeItem->date_of_income_current && $date >= '2023-01-01')
                                {
                                    $isDateChanged = CashFlow::changeDateForIncomeItem($incomeId, $date);
            
                                    if ($isDateChanged)
                                    {
                                        Flash::addMessage('Date ' . '\'' . $date . '\'' . ' assigned to item ' . $incomeNumber, Flash::ORANGE);
                                    }
                                    else
                                    {
                                        Flash::addMessage('Error. Date not changed', Flash::ORANGE);
                                    }  
                                }
                                else
                                {  
                                    if ($singleIncomeItem->date_of_income === $singleIncomeItem->date_of_income_current) 
                                    {
                                        $previousDate = date('Y-m-d', strtotime($singleIncomeItem->date_of_income_current .' -1 day'));
                                        Flash::addMessage('You can use date from 2023-01-01 to ' . $previousDate . ' inclusive', Flash::ORANGE);
                                    }
                                    else
                                    {
                                        Flash::addMessage('You can use date from 2023-01-01 to ' . $singleIncomeItem->date_of_income_current . ' inclusive. You cannot use date ' . $singleIncomeItem->date_of_income, Flash::ORANGE);
                                    }
                                   
                                    $_SESSION['incomeNumber'] = $incomeNumber;
                                }
                                
                            }
                            else
                            {
                                Flash::addMessage('Database error', Flash::ORANGE);
                            }
                        }
                        else
                        {
                            Flash::addMessage('Database error or no incomes items registered', Flash::ORANGE);
                        }
                    }
                    else
                    {
                        Flash::addMessage('Error. Incorrect date provided - this date does not exist', Flash::ORANGE);
                    }
                }
                else
                {
                    Flash::addMessage($errors[0], Flash::ORANGE);
                }
            }
            else
            {
                Flash::addMessage('Error. Income item should have numeric format', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage('Error. No value for income item or date', Flash::ORANGE);
        }  

        $this->redirect('/settings/display-settings/income/change-date');
    }

    public function changeCommentForExpenseItemAction()
    {
        if (!empty($_POST['expenseNumber']) && isset($_POST['comment']))
        {
            $expenseNumber = $_POST['expenseNumber'];
            $comment = $_POST['comment'];
            $isMatched = preg_match("/^[1-9]+\d*$/",$expenseNumber) ? true : false;

            if ($isMatched)
            {
                $comment = Validation::testInput($comment);
                $errors = Validation::validateComment($comment);
            
                if (empty($errors))
                {
                    $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);

                    if (is_array($expenseIdArray) && !empty($expenseIdArray))
                    {
                        $expenseId = $expenseIdArray[(int)$expenseNumber - 1]['id'];
                        $expenseId = (int)$expenseId;

                        $singleExpenseItem = CashFlow::returnSingleExpenseItem($expenseId);

                        if ($singleExpenseItem !== false)
                        {  
                            if ($singleExpenseItem->expense_comment !== $comment)
                            {

                                $isCommentChanged = CashFlow::changeCommentForExpenseItem($expenseId, $comment);
            
                                if ($isCommentChanged)
                                {
                                    Flash::addMessage('New comment assigned to item ' . $expenseNumber, Flash::ORANGE);
                                }
                                else
                                {
                                    Flash::addMessage('Error. Comment not changed', Flash::ORANGE);
                                }  

                            }
                            elseif ($singleExpenseItem->expense_comment === $comment && $comment === "")
                            {
                                Flash::addMessage('Expense item ' . $expenseNumber . ' has no comment, so no changes done', Flash::ORANGE);
                                $_SESSION['expenseNumber'] = $expenseNumber; 
                            }
                            elseif ($singleExpenseItem->expense_comment === $comment && $comment !== "")
                            {
                                Flash::addMessage('This is current comment for expense item ' . $expenseNumber . ' so no changes done', Flash::ORANGE);
                                $_SESSION['expenseNumber'] = $expenseNumber;
                                $_SESSION['comment'] = $comment;
                            }
                            else
                            {
                                Flash::addMessage('Processing error', Flash::ORANGE);
                            }
                        }
                        else
                        {
                            Flash::addMessage('Database error or no expense item found', Flash::ORANGE);
                        }
                    }
                    else
                    {
                        Flash::addMessage('Database error or no expenses items registered', Flash::ORANGE);
                    }
                }
                else
                {
                    $errorArrayLength = count($errors);

                    if ($errorArrayLength > 1)
                    {
                        for ($counter = 0; $counter < $errorArrayLength; $counter++)
                        {
                            $errorNumber = $counter + 1;
                            Flash::addMessage($errorNumber . '. ' . $errors[$counter], Flash::ORANGE);
                        }
                    }
                    else
                    {
                        Flash::addMessage($errors[0], Flash::ORANGE);
                    }
                }
            }
            else
            {
                Flash::addMessage('Error. Expense item should have numeric format', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage('Error. Expense item or comment (or both) does not exist or expense number generated as empty', Flash::ORANGE);
        }  

        $this->redirect('/settings/display-settings/expense/change-comment');
    }

    public function changeCommentForIncomeItemAction()
    {
        if (!empty($_POST['incomeNumber']) && isset($_POST['comment']))
        {
            $incomeNumber = $_POST['incomeNumber'];
            $comment = $_POST['comment'];
            $isMatched = preg_match("/^[1-9]+\d*$/",$incomeNumber) ? true : false;

            if ($isMatched)
            {
                $comment = Validation::testInput($comment);
                $errors = Validation::validateComment($comment);
            
                if (empty($errors))
                {
                    $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);

                    if (is_array($incomeIdArray) && !empty($incomeIdArray))
                    {
                        $incomeId = $incomeIdArray[(int)$incomeNumber - 1]['id'];
                        $incomeId = (int)$incomeId;

                        $singleIncomeItem = CashFlow::returnSingleIncomeItem($incomeId);

                        if ($singleIncomeItem !== false)
                        {  
                            if ($singleIncomeItem->income_comment !== $comment)
                            {

                                $isCommentChanged = CashFlow::changeCommentForIncomeItem($incomeId, $comment);
            
                                if ($isCommentChanged)
                                {
                                    Flash::addMessage('New comment assigned to item ' . $incomeNumber, Flash::ORANGE);
                                }
                                else
                                {
                                    Flash::addMessage('Error. Comment not changed', Flash::ORANGE);
                                }  

                            }
                            elseif ($singleIncomeItem->income_comment === $comment && $comment === "")
                            {
                                Flash::addMessage('Income item ' . $incomeNumber . ' has no comment, so no changes done', Flash::ORANGE);
                                $_SESSION['incomeNumber'] = $incomeNumber; 
                            }
                            elseif ($singleIncomeItem->income_comment === $comment && $comment !== "")
                            {
                                Flash::addMessage('This is current comment for income item ' . $incomeNumber . ' so no changes done', Flash::ORANGE);
                                $_SESSION['incomeNumber'] = $incomeNumber;
                                $_SESSION['comment'] = $comment;
                            }
                            else
                            {
                                Flash::addMessage('Processing error', Flash::ORANGE);
                            }
                        }
                        else
                        {
                            Flash::addMessage('Database error or no income item found', Flash::ORANGE);
                        }
                    }
                    else
                    {
                        Flash::addMessage('Database error or no incomes items registered', Flash::ORANGE);
                    }
                }
                else
                {
                    $errorArrayLength = count($errors);

                    if ($errorArrayLength > 1)
                    {
                        for ($counter = 0; $counter < $errorArrayLength; $counter++)
                        {
                            $errorNumber = $counter + 1;
                            Flash::addMessage($errorNumber . '. ' . $errors[$counter], Flash::ORANGE);
                        }
                    }
                    else
                    {
                        Flash::addMessage($errors[0], Flash::ORANGE);
                    }
                }
            }
            else
            {
                Flash::addMessage('Error. Income item should have numeric format', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage('Error. Income item or comment (or both) does not exist or income number generated as empty', Flash::ORANGE);
        }  

        $this->redirect('/settings/display-settings/income/change-comment');
    }

    public function deleteExpenseItemAction()
    {
        if (!empty($_POST['expenseNumber']))
        {
            $expenseNumber = $_POST['expenseNumber'];
            $isMatched = preg_match("/^[1-9]+\d*$/",$expenseNumber) ? true : false;

            if ($isMatched)
            {
                $expenseIdArray = CashFlow::returnExpensesId($_SESSION['user_id']);

                if (is_array($expenseIdArray) && !empty($expenseIdArray) && isset($expenseIdArray[(int)$expenseNumber - 1]))
                {
                    $expenseId = $expenseIdArray[(int)$expenseNumber - 1]['id'];
                    $expenseId = (int)$expenseId;

                    $isItemDeleted = CashFlow::deleteExpenseItem($expenseId);

                    if ($isItemDeleted)
                    {
                        Flash::addMessage('Expense item deleted', Flash::ORANGE);
                    }
                    else
                    {
                        Flash::addMessage('Error. Expense item not deleted', Flash::ORANGE);
                    }  

                }
                else
                {
                    Flash::addMessage('Database error or no expenses items registered or provided expense number is out of range', Flash::ORANGE);
                }
               
            }
            else
            {
                Flash::addMessage('Error. Expense item should have numeric format', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage('Error. Expense item does not exist or it is generated as empty', Flash::ORANGE);
        }  

        $this->redirect('/settings/display-settings/expense/delete-item');
    }

    public function deleteIncomeItemAction()
    {
        if (!empty($_POST['incomeNumber']))
        {
            $incomeNumber = $_POST['incomeNumber'];
            $isMatched = preg_match("/^[1-9]+\d*$/",$incomeNumber) ? true : false;

            if ($isMatched)
            {
                $incomeIdArray = CashFlow::returnIncomesId($_SESSION['user_id']);

                if (is_array($incomeIdArray) && !empty($incomeIdArray) && isset($incomeIdArray[(int)$incomeNumber - 1]))
                {
                    $incomeId = $incomeIdArray[(int)$incomeNumber - 1]['id'];
                    $incomeId = (int)$incomeId;

                    $isItemDeleted = CashFlow::deleteIncomeItem($incomeId);

                    if ($isItemDeleted)
                    {
                        Flash::addMessage('Income item deleted', Flash::ORANGE);
                    }
                    else
                    {
                        Flash::addMessage('Error. Income item not deleted', Flash::ORANGE);
                    }  

                }
                else
                {
                    Flash::addMessage('Database error or no incomes items registered or provided income number is out of range', Flash::ORANGE);
                }
               
            }
            else
            {
                Flash::addMessage('Error. Income item should have numeric format', Flash::ORANGE);
            }
        }
        else
        {
            Flash::addMessage('Error. Income item does not exist or it is generated as empty', Flash::ORANGE);
        }  

        $this->redirect('/settings/display-settings/income/delete-item');
    }

}
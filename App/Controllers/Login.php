<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;
use \App\Auth;

/**
 * Login controller
 *
 * PHP version 7.2.0
 */

class Login extends OpenAccess 
{
    /**
     * Show the login page 
     * 
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Login/login.html'); 

 //    if (preg_match("/^([a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+)* ?[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]+$/","Adam Stępniewski")) {
 //      echo "Works";
 //       }
  //  "/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ]*$/"
   /*  if (!preg_match("/^([a-zA-Z]+)* ?[a-zA-Z]+$/",$this->username)) {
        $this->errors[] = "Only letters and one space allowed in name. Please use standard English characters";
    }  */
   //  $prev_date = date('Y-m-d', strtotime('2024-05-04' .' -1 day'));
   //  var_dump(empty(['0']));
   //     var_dump(preg_match("/^\d+\.\d\d$/","34.345")); 
    //    var_dump(!empty($_SESSION['flash_notifications']));
    //    var_dump(\App\Models\CashFlow::returnExpensesId(5));
     //   View::renderTemplate('Settings/expense_list.html'); 
     //   var_dump(\App\Models\CashFlow::checkIfCategoryPresentOnExpensesList(5,"traNsport"));
   //  var_dump(ucfirst(strtolower("dEBit CaRd")));

  // var_dump(empty(\App\Models\CashFlow::returnCategoriesAssignedToUser(5)));
  //var_dump(empty(false));
    }

    /**
     * Show the no authorization page - for users not logged in 
     * 
     * @return void
     */
    public function blockAccessAction()
    {
        View::renderTemplate('Login/no_authorization.html');
    }

    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        
        $remember_me = isset($_POST['remember-me']);

        if ($user) {

            Auth::login($user, $remember_me);

     //       Flash::addMessage('Login successful');

            $this->redirect('/menu/display');

        } else {

            Flash::addMessage('Login data incorrect. Please try again', Flash::ORANGE);

            View::renderTemplate('Login/login.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
                
            ]);
        }
    }


    /**
     * Show a "logged out" flash message and redirect to the homepage. Necessary to use the flash messages
     * as they use the session and at the end of the logout method (destroyAction) the session is destroyed
     * so a new action needs to be called in order to use the session.
     *
     * @return void
     */
    public function showLogoutMessageAction()
    {
        Flash::addMessage('Logout successful', Flash::ORANGE);
        
        $this->redirect('/');
    }


    /**
     * Show a flash message about account deletion and redirect to the homepage.
     *
     * @return void
     */
    public function redirectAfterDeletionAction()
    {
        Flash::addMessage('Account deleted', Flash::ORANGE);
        
        $this->redirect('/');
    }

}
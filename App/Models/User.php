<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

/**
 * User model
 *
 * PHP version 7.2.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];


    
    
    /**
     * Class constructor
     * 
     * @param array $data Initial property values (optional)
     * 
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }


    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue(); 

            $sql = 'INSERT INTO users (username, email, password, activation_hash)
                    VALUES (:username, :email, :password, :activation_hash)';

            $dbConnection = static::getDB();
            $stmt = $dbConnection->prepare($sql);

            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
            $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }




    /** 
     * Output escaping
     * @param string $data One of the registation data - name, email or password
     * 
     * @return string name, email or password after output escaping
    */
    public static function testInput($data) {
        $data = trim($data);
        $data = htmlspecialchars($data);
        return $data;
    }


    /**
     * Find a user model by email address
     * @param string $email email address to search for
     * 
     * @return mixed User object if found, false otherwise
     */
    public static function checkIfEmailExistInDatabase($email)
    {
        $sql = "SELECT *
                FROM users
                WHERE email = :email";
        
        $dbConnection = static::getDB();
        $stmt =  $dbConnection->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     * @param string $ignore_id Return false anyway if the record found has this ID
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExistsIgnoreIfNeeded($email, $ignore_id = null)
    {
        $user = static::checkIfEmailExistInDatabase($email);

        if ($user) {
            if ($user->id != $ignore_id) {
                return true;
            }
        }

        return false;
    }


    /**
     * Find a user model by user ID
     * @param string $user_id user ID to search for
     * 
     * @return mixed User object if found, false otherwise
     */
    public static function checkIfUserIdExistInDatabase($user_id)
    {
        $sql = "SELECT *
                FROM users
                WHERE id = :id";
        
        $dbConnection = static::getDB();
        $stmt =  $dbConnection->prepare($sql);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * Validation for name, email and password. Error array is populated with message if any error is detected
     * 
     * @return void
     */
    public function validate() {
        //Output escaping
        $this->username = static::testInput($this->username);
        $this->email = static::testInput($this->email);
        $this->password = trim($this->password);
        
        //Name
        if ($this->username == '') {
            $this->errors[] = 'Name is required';
        }
        elseif ($this->username != '') {
            if (!preg_match("/^([a-zA-Z]+)* ?[a-zA-Z]+$/",$this->username)) {
                $this->errors[] = "Only letters and one space allowed in name. Please use standard English characters";
            }
        }
        
        //Email
        if ($this->email == '') {
            $this->errors[] = 'Email is required';
        }
        elseif ($this->email != '') {
            $this->email = filter_var($this->email, FILTER_SANITIZE_EMAIL);
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Invalid email format";
            }
            elseif (static::emailExistsIgnoreIfNeeded($this->email, $this->id ?? null)) {
                $this->errors[] = "Error. Provided email exists";
            }
        }

        //Password
        if ($this->password == '') {
            $this->errors[] = 'Password is required';
        }
        elseif ($this->password !='') {
            $uppercase = preg_match('@[A-Z]@', $this->password);
            $lowercase = preg_match('@[a-z]@', $this->password);
            $number    = preg_match('@[0-9]@', $this->password);
            $specialChars = preg_match('@[^\w]@', $this->password);
            
            if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($this->password) < 10 ) {
                $this->errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, one number and one special character. Length at least 10 characters';
            }
        }
    }

    /**
     * Authenticate a user by email and password
     * 
     * @param string $email email address
     * @param string $password password
     * 
     * @return mixed  The user object or false if authentication fails
     */
    public static function authenticate($email, $password)
    {
        $user = static::checkIfEmailExistInDatabase($email);

        if ($user && $user->is_active) {
            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        return false;
    }

    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Send password reset instructions to the user specified
     *
     * @param string $email The email address
     *
     * @return void
     */
    public static function sendPasswordReset($email)
    {
        $user = static::checkIfEmailExistInDatabase($email);

        if ($user) {

            if ($user->startPasswordReset()) {

                if($user->sendPasswordResetEmail()) {
                    return true;
                }

            }
        }
        return false;
    }

    /**
     * Start the password reset process by generating a new token and expiry
     *
     * @return void
     */
    protected function startPasswordReset()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->password_reset_token = $token->getValue();

        $expiry_timestamp = time() + 60 * 60 * 1;  // 1 hour from now

        $sql = 'UPDATE users
                SET password_reset_hash = :token_hash,
                    password_reset_exp_at = :expires_at
                WHERE id = :id';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Send password reset instructions in an email to the user
     *
     * @return boolean true if email sent, false otherwise
     */
    protected function sendPasswordResetEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;

        $text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = View::getTemplate('Password/reset_email.html', ['url' => $url]);
    
        return Mail::send($this->email, 'Password reset', $text, $html);
    }

        /**
     * Find a user model by password reset token and expiry
     *
     * @param string $token Password reset token sent to user
     *
     * @return mixed User object if found and the token hasn't expired, null otherwise
     */
    public static function findByPasswordReset($token)
    {
        $token = new Token($token);
        $hashed_token = $token->getHash();

        $sql = 'SELECT * FROM users
                WHERE password_reset_hash = :token_hash';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        $user = $stmt->fetch();

        if ($user) {
            
            // Check password reset token hasn't expired
            if (strtotime($user->password_reset_exp_at) > time()) {

                return $user;
            }
        }
    }

    /**
     * Reset the password
     *
     * @param string $password The new password
     *
     * @return boolean  True if the password was updated successfully, false otherwise
     */
    public function resetPassword($password)
    {
        $this->password = $password;

        $this->validate();

        //return empty($this->errors);
        if (empty($this->errors)) {

            $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

            $sql = 'UPDATE users
                    SET password = :password_hash,
                        password_reset_hash = NULL,
                        password_reset_exp_at = NULL
                    WHERE id = :id';

            $dbConnection = static::getDB();
            $stmt = $dbConnection->prepare($sql);
                                                  
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
                                          
            return $stmt->execute();
        }

        return false;
    }

    /**
     * Send an email to the user containing the activation link
     *
     * @return void
     */
    public function sendActivationEmail()
    {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;

        $text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);

        Mail::send($this->email, 'Account activation', $text, $html);
    }

    /**
     * Activate the user account with the specified activation token
     *
     * @param string $value Activation token from the URL
     *
     * @return void
     */
    private static function connectDatabaseWithActivationToken($sql, $value, $sqlType="Select")
    {
        $token = new Token($value);
        $hashed_token = $token->getHash();

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);

        $stmt->execute();

        if ($sqlType == "Select")
        {
            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
            return $stmt->fetch();
        }
    }

    
    public static function activateAccount($value)
    {
        $sql = 'UPDATE users
                SET is_active = 1
                WHERE activation_hash = :hashed_token';
    
        static::connectDatabaseWithActivationToken($sql, $value,"Update");
    }

    public static function returnAccountStatus($value)
    {
        $sql = 'SELECT id, is_active FROM users
                WHERE activation_hash = :hashed_token';
    
        return static::connectDatabaseWithActivationToken($sql, $value);
    }

    public static function clearActivationToken($value)
    {
        $sql = 'UPDATE users
                SET activation_hash = null
                WHERE activation_hash = :hashed_token';
    
        static::connectDatabaseWithActivationToken($sql, $value, "Update");
    }

    public function returnDefaultCategoriesForIncome() 
    {
        $sql = 'SELECT *
                FROM incomes_category_default';

        $dbConnection = static::getDB();
        $stmt =  $dbConnection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignDefaultCategories() 
    {
        $defaultCategories = $this->returnDefaultCategoriesForIncome();
        
        $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name)
                VALUES 
                (:user_id, :category_name0),
                (:user_id, :category_name1),
                (:user_id, :category_name2),
                (:user_id, :category_name3)';

        $dbConnection = static::getDB();
        $stmt = $dbConnection->prepare($sql);

        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);    
        
        for ($categoryCounter = 0; $categoryCounter < count($defaultCategories); $categoryCounter++) 
        {
            $categoryName = ':category_name' . $categoryCounter;
            $stmt->bindValue($categoryName, $defaultCategories[$categoryCounter]['name'], PDO::PARAM_STR);   
        }
                                  
        return $stmt->execute();
    }

    public static function returnCategoriesForIncome($userId) 
    {
        $sql = 'SELECT *
                FROM incomes_category_assigned_to_users
                WHERE user_id = :user_id';

        $dbConnection = static::getDB();
        $stmt =  $dbConnection->prepare($sql);

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);  

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
       
      /* 
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        */
    }

}

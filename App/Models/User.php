<?php

namespace App\Models;

use PDO;
use \App\Token;

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

        /*    $token = new Token();
            $hashed_token = $token->getHash();
            $this->activation_token = $token->getValue(); */

            $sql = 'INSERT INTO users (username, email, password)
                    VALUES (:username, :email, :password)';

            $dbConnection = static::getDB();
            $stmt = $dbConnection->prepare($sql);

            $stmt->bindValue(':username', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password_hash, PDO::PARAM_STR);
       //     $stmt->bindValue(':activation_hash', $hashed_token, PDO::PARAM_STR);

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
        $this->name = static::testInput($this->name);
        $this->email = static::testInput($this->email);
        $this->password = trim($this->password);
        
        //Name
        if ($this->name == '') {
            $this->errors[] = 'Name is required';
        }
        elseif ($this->name != '') {
            if (!preg_match("/^([a-zA-Z]+)* ?[a-zA-Z]+$/",$this->name)) {
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
            elseif (static::checkIfEmailExistInDatabase($this->email)) {
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

        if ($user) {
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

}

<?php

namespace App\Models;

use PDO;
use \App\Token;

/**
 * Remembered login model
 *
 * PHP version 7.2.0
 */
class RememberedLogin extends \Core\Model
{

    /**
     * Find a remembered login model by the token
     *
     * @param string $token The remembered login token
     *
     * @return mixed Remembered login object if found, false otherwise
     */
    public static function findByToken($token)
    {
        $token = new Token($token);
        $token_hash = $token->getHash();

        $sql = 'SELECT * FROM remembered_logins
                WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    public function hasExpired()
    {
        return strtotime($this->expires_at) < time();
    }

    public function getUser()
    {
        return User::checkIfUserIdExistInDatabase($this->user_id);
    }

    /**
     * Delete this model
     *
     * @return void
     */
    public function delete()
    {
        $sql = 'DELETE FROM remembered_logins
                WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

        $stmt->execute();
    }

}
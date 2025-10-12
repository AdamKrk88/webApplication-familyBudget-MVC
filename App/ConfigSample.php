<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.2.0
 */
class ConfigSample
{

    /**
     * Database host
     * @var string
     */
    private const DB_HOST = 'your_database_host';

    /**
     * Database name
     * @var string
     */
    private const DB_NAME = 'your_database_name';

    /**
     * Database user
     * @var string
     */
    private const DB_USER = 'database_user';

    /**
     * Database password
     * @var string
     */
    private const DB_PASSWORD = 'database_password';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    private const SHOW_ERRORS = false;

    /**
     * Secret key for hashing
     * @var string
     */
    private const SECRET_KEY = ''; // generate secret key for hashing

    /**
     * SMTP server to send through
     * @var string
     */
    private const PHPMAILER_HOST = 'provide_smtp_server_host';

    /**
     * SMTP username
     * @var string
     */
    private const PHPMAILER_USERNAME = 'your_email';

    /**
     * SMTP password
     * @var string
     */
    private const PHPMAILER_PASSWORD = ''; // generate smtp password. Use App Password if you use gmail

     /**
     * Sender name
     * @var string
     */
    private const PHPMAILER_SENDER_NAME = ''; // your sender name. It will be visible for email recipient

    /**
     * Get databse host
     *
     * @return string
     */
    public static function getDbHost() {
        return self::DB_HOST;
    }

    /**
     * Get databse name
     *
     * @return string
     */
    public static function getDbName() {
        return self::DB_NAME;
    }

    /**
     * Get databse user
     *
     * @return string
     */
    public static function getDbUser() {
        return self::DB_USER;
    }

    /**
     * Get databse password
     *
     * @return string
     */
    public static function getDbPassword() {
        return self::DB_PASSWORD;
    }

    /**
     * Get show error setting - true or false
     *
     * @return boolean
     */
    public static function getShowErrors() {
        return self::SHOW_ERRORS;
    }

    /**
     * Get secret key for hashing
     *
     * @return string
     */
    public static function getSecretKey() {
        return self::SECRET_KEY;
    }

    /**
     * Get SMTP server host
     *
     * @return string
     */
    public static function getPhpmailerHost() {
        return self::PHPMAILER_HOST;
    }

    /**
     * Get SMTP username
     *
     * @return string
     */
    public static function getPhpmailerUsername() {
        return self::PHPMAILER_USERNAME;
    }

    /**
     * Get SMTP password
     *
     * @return string
     */
    public static function getPhpmailerPassword() {
        return self::PHPMAILER_PASSWORD;
    }

    /**
     * Get sender name 
     *
     * @return string
     */
    public static function getPhpmailerSenderName() {
        return self::PHPMAILER_SENDER_NAME;
    }

}
<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.2.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'budget_manager_mvc';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'bm_mvc';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'G2kIMVmorbNnjb5a';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret key for hashing
     * @var string
     */
    const SECRET_KEY = '3hu08ZclcdCIUkzrBnSuncyhecRNuykH';
}

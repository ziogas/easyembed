<?php

/*
 * Main config place.
 * Use whatever logic you want as long as it returns array.
 */
$config = array(
    'error_level' => E_ALL,
    'log_errors' => true,
    'timezone' => 'Europe/London',
    'locale' => 'en_US',
    'session' => array(
        'name' => 'session',
        'auto_start' => 0,
        'gc_maxlifetime' => 21600,
        'cookie_lifetime' => 21600,
        'use_only_cookies' => 1,

        // Remove those if you don't have memcached support
        'save_handler' => 'memcached',
        'save_path' => '127.0.0.1:11211',
    ),
    'databases' => array(
        'main' => array(
            'dsn' => 'mysql:host=localhost;dbname=test',
            'username' => 'root',
            'password' => '',
            'params' => array(
                // If mysqlnd isn't installed then MYSQL_ATTR_INIT_COMMAND constant might be undefined
                1002 => "SET NAMES 'UTF8'",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ),
/*
            // Mysql setup example
            'dsn' => 'mysql:host=localhost;dbname=test',
            'username' => 'root',
            'password' => '',
            'params' => array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION),
*/
/*
            // Sqlite setup example
            'dsn' => 'sqlite:/home/user/test.sq3',
            'params' => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION),
*/
/*
            // Pgsql setup example
            'dsn' => 'pgsql:host=localhost;dbname=test;user=user;password=password',
            'params' => array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION),
*/
        ),
    ),
);

// Some specific rule to define current enviroment. Can be anything and not only dev or prod
$enviroment = (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost') ? 'dev' : 'prod';

if ($enviroment == 'prod') {
    $config['log_errors'] = true;
}

return $config;

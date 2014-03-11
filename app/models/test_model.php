<?php

/*
 * Sample test model.
 * Loads pdo with main database config.
 */
class test_model
{
    private $pdo;

    public function __construct ()
    {
        $this -> pdo = db_pdo::load ( 'main' );
    }
}

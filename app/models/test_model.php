<?php

/*
 * Sample test model.
 * Loads pdo with main database config.
 */
class test_model extends base_model
{
    public function __construct ()
    {
        parent::__construct();
        //$this->set_table('test');
    }
}

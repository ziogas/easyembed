<?php

/*
 * Standard PHPUnit testing
 * You can do EE::init method in contructor to initialize framework.
 * Call it directly or via help of cli script in main directory:
 * ./cli test_app
 * OR
 * ./cli test app/exampleTest.php
 */
class exampleTest extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
    }

    public function testStuff()
    {
        $this->assertSame('Stuff', 'Stuff');
    }
}

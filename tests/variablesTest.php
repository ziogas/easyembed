<?php

class variablesTest extends PHPUnit_Framework_TestCase
{
    public function testVariableSet ()
    {
        $this -> assertTrue ( EE::set ( 'foo', 'bar' ) );
        $this -> assertTrue ( EE::set ( 'x', array () ) );
        $this -> assertTrue ( EE::set ( 'x', 'y', 'z' ) );
    }

    public function testVariableGet ()
    {
        $this -> assertSame ( 'bar', EE::get ( 'foo' ) );
        $this -> assertArrayHasKey ( 'y', EE::get ( 'x' ) );
        $this -> assertSame ( 'z', EE::get ( 'x', 'y' ) );
        $this -> assertNull ( EE::get ( 'cat' ) );
    }

    public function testVariableRefs ()
    {
        $ref = &EE::get_ref ( 'x' );
        $this -> assertSame ( 'z', $ref [ 'y' ] );
        $ref [ 'y' ] = 'zzz';
        $this -> assertSame ( 'zzz', EE::get ( 'x', 'y' ) );

        $ref2 = &EE::get_ref ( 'foo' );
        $this -> assertSame ( 'bar', $ref2 );
        $ref2 = 'oof';
        $this -> assertSame ( 'oof', EE::get ( 'foo' ) );
    }

    public function testVariableIsSet ()
    {
        $this -> assertTrue ( EE::is_set ( 'foo' ) );
        $this -> assertFalse ( EE::is_set ( 'cat' ) );
        $this -> assertTrue ( EE::is_set ( 'x', 'y' ) );
        $this -> assertFalse ( EE::is_set ( 'x', 'y', 'dog' ) );
    }

    public function testVariableDelete ()
    {
        $this -> assertSame ( 'oof', EE::get ( 'foo' ) );
        $this -> assertTrue ( EE::un_set ( 'foo' ) );
        $this -> assertNull ( EE::get ( 'foo' ) );

        $this -> assertFalse ( EE::un_set ( 'cat', 'dog', 'x' ) );
        $this -> assertTrue ( EE::un_set ( 'x', 'y' ) );

        $this -> assertTrue ( EE::un_set ( 'x' ) );
    }
}

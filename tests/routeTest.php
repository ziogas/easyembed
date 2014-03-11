<?php

class routeTest extends PHPUnit_Framework_TestCase
{
    public function testGlobalRouting ()
    {
        $routes = array
        (
            'base_path' => '',
            'routes' => array
            (
                '/another/:num' => array ( 'controller' => 'test', 'action' => 'another' ),
                '/something' => array ( 'controller' => 'test', 'action' => 'something' ),
                '/something/:any' => array ( 'controller' => 'test', 'action' => 'something' ),
                '/custom/(.+?)/(.+?)' => array ( 'controller' => 'test', 'action' => 'custom' ),
                '/custom/(.+?)-(.+?)' => array ( 'controller' => 'test', 'action' => 'custom' ),
                '/:any' => array ( 'controller' => 'test' ),
            )
        );

        $this -> assertSame ( null, null, EE::route ( '', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test' ), array ( '/' ) ), EE::route ( '/', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test' ), array ( '/here/I/come', 'here/I/come' ) ), EE::route ( '/here/I/come', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'something' ), null ), EE::route ( '/something', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'something' ), array ( '/something/is', 'is' ) ), EE::route ( '/something/is', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'something' ), array ( '/something/is/here', 'is/here' ) ), EE::route ( '/something/is/here', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'another' ), array ( '/another/20', '20' ) ), EE::route ( '/another/20', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test' ), array ( '/another/not_number', 'another/not_number' ) ), EE::route ( '/another/not_number', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'custom' ), array ( '/custom/regex/here', 'regex', 'here' ) ), EE::route ( '/custom/regex/here', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'custom' ), array ( '/custom/regex-here', 'regex', 'here' ) ), EE::route ( '/custom/regex-here', $routes ) );
    }

    public function testBasePathRouting ()
    {
        $routes = array
        (
            'base_path' => '/testing',
            'routes' => array
            (
                '/another/:num' => array ( 'controller' => 'test', 'action' => 'another' ),
                '/something' => array ( 'controller' => 'test', 'action' => 'something' ),
                '/something/:any' => array ( 'controller' => 'test', 'action' => 'something' ),
                '/custom/(.+?)/(.+?)' => array ( 'controller' => 'test', 'action' => 'custom' ),
                '/:any' => array ( 'controller' => 'test' ),
            )
        );

        //$this -> assertSame ( null, null, EE::route ( '/', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test' ), array ( '/testing/' ) ), EE::route ( '/testing/', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test' ), array ( '/testing/here/I/come', 'here/I/come' ) ), EE::route ( '/testing/here/I/come', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'something' ), null ), EE::route ( '/testing/something', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'something' ), array ( '/testing/something/is', 'is' ) ), EE::route ( '/testing/something/is', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'something' ), array ( '/testing/something/is/here', 'is/here' ) ), EE::route ( '/testing/something/is/here', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'another' ), array ( '/testing/another/20', '20' ) ), EE::route ( '/testing/another/20', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test' ), array ( '/testing/another/not_number', 'another/not_number' ) ), EE::route ( '/testing/another/not_number', $routes ) );
        $this -> assertSame ( array ( array ( 'controller' => 'test', 'action' => 'custom' ), array ( '/testing/custom/regex/here', 'regex', 'here' ) ), EE::route ( '/testing/custom/regex/here', $routes ) );
    }
}

<?php

class frameworkTest extends PHPUnit_Framework_TestCase
{
    public function testGlobal ()
    {
        $config = array ();
        $routes = array
        (
            'base_path' => '',
            'routes' => array
            (
                '/another/:num' => array ( 'controller' => 'test', 'action' => 'another' ),
                '/custom/(.+?)/(.+?)' => array ( 'controller' => 'test', 'action' => 'custom' ),
                '/something/:any' => array ( 'controller' => 'test', 'action' => 'something' ),
                '/:any' => array ( 'controller' => 'test' ),
            )
        );

        EE::init ( $config, $routes );

        $this -> assertSame ( $routes, EE::get ( '_routes' ) );

        // Default index route
        $this -> assertSame ( 'index', EE::load_page ( false, '/' ) );

        // Auto mapped route
        $this -> assertSame ( 'foobarbaz', EE::load_page ( false, '/foobar' ) );

        // Not existing route
        $this -> assertSame ( 'Not Found', EE::load_page ( false, '/non_existing_url' ) );

        // Mapped route
        $this -> assertSame ( 'something', EE::load_page ( false, '/something' ) );

        // Dynamic Mapped route
        $this -> assertSame ( 'another ', EE::load_page ( false, '/another/' ) );

        // Dynamic Mapped route
        $this -> assertSame ( 'another 22', EE::load_page ( false, '/another/22' ) );

        // Custom route
        $this -> assertSame ( 'regex-here', EE::load_page ( false, '/custom/regex/here' ) );

    }

    public function testWithBasePath ()
    {
        $config = array ();
        $routes = array
        (
            'base_path' => '/testing',
            'routes' => array
            (
                '/another/:num' => array ( 'controller' => 'test', 'action' => 'another' ),
                '/custom/(.+?)/(.+?)' => array ( 'controller' => 'test', 'action' => 'custom' ),
                '/something/:any' => array ( 'controller' => 'test', 'action' => 'something' ),
                '/:any' => array ( 'controller' => 'test' ),
            )
        );

        EE::init ( $config, $routes );

        $this -> assertSame ( $routes, EE::get ( '_routes' ) );

        // Default index route
        $this -> assertSame ( 'index', EE::load_page ( false, '/testing/' ) );

        // Auto mapped route
        $this -> assertSame ( 'foobarbaz', EE::load_page ( false, '/testing/foobar' ) );

        // Not existing route
        $this -> assertSame ( 'Not Found', EE::load_page ( false, '/testing/non_existing_url' ) );

        // Mapped route
        $this -> assertSame ( 'something', EE::load_page ( false, '/testing/something' ) );

        // Dynamic Mapped route
        $this -> assertSame ( 'another ', EE::load_page ( false, '/testing/another/' ) );

        // Dynamic Mapped route
        $this -> assertSame ( 'another 2', EE::load_page ( false, '/testing/another/2' ) );

        // Custom route
        $this -> assertSame ( 'regex-here', EE::load_page ( false, '/testing/custom/regex/here' ) );
    }
}

// Dummy controller
class test_controller
{
    public function index_action ()
    {
        echo 'index';
    }

    public function foobar_action ()
    {
        echo 'foobarbaz';
    }

    public function another_action ( $path )
    {
        echo 'another '. $path [ 0 ];
    }

    public function something_action ( $path )
    {
        echo 'something';
    }

    public function custom_action ( $path )
    {
        echo $path [ 0 ] .'-'. $path [ 1 ];
    }
}

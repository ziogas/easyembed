<?php

/*
 * All the routing rules must be defined here.
 * Use whatever logic you want as long as it returns array.
 */
return array
(
    // Base path without trailing slash
    'base_path' => '/ee',

    // App routes
    // Route url => app controller and action
    // Can be regex or :any, :string, :num, :alpha
    'routes' => array
    (
        '/bar/:any' => array ( 'controller' => 'test', 'action' => 'custom_bar' ),
        '/:any' => array ( 'controller' => 'test' ),
    ),
);

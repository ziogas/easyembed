<?php
// Bootstrap file

// Require main framework class
require ( dirname ( __FILE__ ) .'/ee.php' );

// Initialize and load configs/routes
EE::init ();

// Load and execute page
EE::load_page ();

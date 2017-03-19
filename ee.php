<?php

/*
 * EasyEmbed ( EE ) is a micro framework optimized for embeding it into already coded custom enviroments and systems.
 * The primary goals of EE are to be flexible, ligtweight/high performance and portable way to design mini-apps inside another projects. However it can be used as a stand-alone application too.
 * If you stuck on old project but want to have some custom functionality without digging into code or make mini-app that can be used everywhere then EE is just for you.
 *
 * EE Proposed structure:
 * /
 * /.htaccess           <- Needed to ensure that all requests are made through index.php. If you use nginx just look at nginx.conf examples.
 * /index.php           <- This is the main bootstrap file which fires everything. Those 3 lines of code written here can be ported anywhere.
 * /config.php          <- Main config file. Whatever format and content you want, as long as it returns an array.
 * /routes.php          <- Main routes definitions. Used to define base path and main app routes.
 * /ee.php              <- Main framework file. All framework class methods live here.
 * /app/                <- Application directory. All the application related code should be here.
 *   /app/controllers/  <- Self explaining
 *   /app/models/       <- Self explaining
 *   /app/views/        <- Self explaining
 *   /app/assets/       <- Self explaining
 *   /app/tests/        <- Self explaining
 * /libs/               <- All shared libraries.
 * /tests/              <- Unit tests of framework.
 * /cli                 <- File for the all cli actions/helpers.
 *
 * Requirements: PHP >= 5.1.3, PDO for database usage. Also if you want to use unit testing you'll need PHP >= 5.3.
 * License: MIT
 * Source: http://github.com/ziogas/easyembed.git
 * Author: Arminas Žukauskas - arminas@ini.lt
 */

// Failsafe check
if (!class_exists('EE')) {
    class EE
    {
        // Directory where the app lives
        const APP_DIR = 'app';

        // Directory where the app models live
        const APP_CONTROLLERS_DIR = 'app/controllers';

        // Directory where the app models live
        const APP_MODELS_DIR = 'app/models';

        // Directory where the app models live
        const APP_VIEWS_DIR = 'app/views';

        // Directory where are all the tranlations
        const APP_TRANSLATIONS_DIR = 'app/translations';

        // Application config file
        const APP_CONFIG = 'app/config.php';

        // Application routes file
        const APP_ROUTES = 'app/routes.php';

        // Directory where all the application libs live
        const APP_LIBS_DIR = 'app/libs';

        // Directory where all the shared libs live
        const LIBS_DIR = 'libs';

        // on init () this will be current directory
        public static $dir = false,
                       // variables holder
                       $variables = array(),
                       // config holder
                       $config = array(),
                       // main routes holder
                       $routes = array(),
                       // model instances holder
                       $models = array(),
                       // controller instances holder
                       $controllers = array();

        /*
         * Main initialization method.
         * Sets directory, loads config and route files if needed.
         * Also can interact with various config options
         *
         * @param array|null $config Config array. Leave null for loading default config.php
         * @param array|null $routes Routes array. Leave null for loading default routes.php
         *
         * return boolean
         */
        public static function init($config = null, $routes = null)
        {
            // Set current directory for correct app includes
            self::$dir = dirname(__FILE__);
            EE::set('_dir', self::$dir);

            // Use provided or load config from file
            if (!is_null($config)) {
                self::$config = $config;
            } else {
                self::$config = require(self::$dir.'/'.self::APP_CONFIG);
            }

            EE::set('_config', self::$config);

            // Define error level
            if (isset(self::$config['error_level'])) {
                error_reporting(self::$config['error_level']);
            }

            // Define whenever to log errors
            if (isset(self::$config['log_errors'])) {
                ini_set('log_errors', self::$config['log_errors']);
            }

            // Define whenever to display errors
            if (isset(self::$config['display_errors'])) {
                ini_set('display_errors', self::$config['display_errors']);
            }

            // Define timezone
            if (isset(self::$config['timezone'])) {
                date_default_timezone_set(self::$config['timezone']);
            }

            // Use provided or load routes file
            if (!is_null($routes)) {
                self::$routes = $routes;
            } else {
                self::$routes = require(self::$dir.'/'.self::APP_ROUTES);
            }

            EE::set('_routes', self::$routes);

            // Everything went fine
            return true;
        }

        /**
         * Autoloader for controllers, models and libraries
         *
         * @param string $classname Classname to load
         */
        public static function autoload($classname)
        {
            if (substr($classname, -11) === '_controller') {
                require(self::$dir .'/'. self::APP_CONTROLLERS_DIR .'/'. $classname .'.php');
            } elseif (substr($classname, -6) === '_model') {
                require(self::$dir .'/'. self::APP_MODELS_DIR .'/'. $classname .'.php');
            } elseif (substr($classname, 0, 5) === 'Zend_') {
                $classname = str_replace('_', '/', substr($classname, 5));
                if (file_exists(self::$dir .'/'. self::APP_LIBS_DIR .'/Zend/'. $classname .'.php')) {
                    $current_dir = getcwd();
                    chdir(self::$dir .'/'. self::APP_LIBS_DIR);
                    require(self::$dir .'/'. self::APP_LIBS_DIR .'/Zend/'. $classname .'.php');
                    chdir($current_dir);
                } elseif (file_exists(self::$dir .'/'. self::LIBS_DIR .'/Zend/'. $classname .'.php')) {
                    $current_dir = getcwd();
                    chdir(self::$dir .'/'. self::LIBS_DIR);
                    require(self::$dir .'/'. self::LIBS_DIR .'/Zend/'. $classname .'.php');
                    chdir($current_dir);
                }
            } elseif (file_exists(self::$dir .'/'. self::APP_LIBS_DIR .'/'. $classname .'.php')) {
                require(self::$dir .'/'. self::APP_LIBS_DIR .'/'. $classname .'.php');
            } elseif (file_exists(self::$dir .'/'. self::LIBS_DIR .'/'. $classname .'.php')) {
                require(self::$dir .'/'. self::LIBS_DIR .'/'. $classname .'.php');
            }
        }

        /**
         * Main method to load or return page
         *
         * @param boolean $print_output Whenever to print or return output
         *
         * return string|boolean
         */
        public static function load_page($print_output = true, $uri = null)
        {
            if (is_null($uri)) {
                $uri = false;

                // First try to get correct uri from request_uri
                if (isset($_SERVER['REQUEST_URI'])) {
                    $qpos = strpos($_SERVER['REQUEST_URI'], '?');
                    $uri = $qpos ? substr($_SERVER['REQUEST_URI'], 0, $qpos) : $_SERVER['REQUEST_URI'];
                }
                // Fallback to path_info
                elseif (isset($_SERVER['PATH_INFO'])) {
                    $uri = $_SERVER['PATH_INFO'];
                }
            }

            // Bad request was made
            if (!$uri || (!empty(self::$routes['base_path']) && strpos($uri, self::$routes['base_path']) !== 0)) {
                return self::error_page(400, null, $print_output);
            }

            list($handler, $matches) = self::route($uri);

            // No handler match
            if (!$handler) {
                return self::error_page(404, null, $print_output);
            }

            if ($matches) {
                // Remove array 0th item from preg match
                array_shift($matches);

                // If we have simple match then explode by slashes
                if (sizeof($matches) === 1) {
                    $matches = explode('/', $matches[0]);
                }
            }

            if ($print_output) {
                self::load_app($handler, $matches, $print_output);
            } else {
                ob_start();

                self::load_app($handler, $matches, $print_output);
                return ob_get_clean();
            }
        }

        /**
         * Method to route app
         *
         * @param string $uri Uri to route
         * @param array|null $routes Custom routes config
         *
         * return array
         */
        public static function route($uri, $routes = null)
        {
            if (is_null($routes)) {
                $routes = self::$routes;
            }

            // Strip base path from url
            $relative_uri = substr($uri, strlen($routes['base_path']));

            $matched_handler = $matched_matches = null;

            // Try to discover needed route
            if (isset($routes['routes']) && sizeof($routes['routes'])) {
                // Just a simple lookup
                if (isset($routes['routes'][$relative_uri])) {
                    $matched_handler = $routes['routes'][$relative_uri];
                } else {
                    // Replace tokens
                    $regex_tokens = array(
                        ':any' => '(.*)',
                        ':string' => '([a-zA-Z]+)',
                        ':num' => '([0-9]+)',
                        ':alpha' => '([a-zA-Z0-9\-_]+)',
                    );

                    $regex_keys = array_keys($regex_tokens);

                    foreach ($routes [ 'routes' ] as $route_config => $handler) {
                        $route_config = str_replace($regex_keys, $regex_tokens, $route_config);

                        if (preg_match('#^'. preg_quote($routes['base_path'], '#') . $route_config .'$#i', $uri, $matches)) {
                            $matched_handler = $handler;
                            $matched_matches = $matches;

                            break;
                        }
                    }
                }
            }

            if (sizeof($matched_matches) > 1 && end($matched_matches) === '') {
                array_pop($matched_matches);
            }

            return array($matched_handler, $matched_matches);
        }

        /**
         * Method to load and execute app
         *
         * @param string $handler Handler config
         * @param array $matches Matches array from url
         * @param boolean $print_output Whenever to print or return output
         *
         * return void
         */
        public static function load_app($handler, $matches, $print_output)
        {
            if (!isset($handler['controller'])) {
                return self::error_page(500, null, true);
            }

            $controller = EE::controller($handler['controller']);

            if (isset($handler['action'])) {
                $action = $handler['action'];
            } else {
                if (isset($matches[0]) && !empty($matches[0])) {
                    $action = strtolower(array_shift($matches));
                } else {
                    $action = 'index';
                }
            }

            $action .= '_action';

            if (!method_exists($controller, $action)) {
                return self::error_page(404, null, true);
            }

            $controller->$action($matches);
        }

        /*
         * Method to load app model
         *
         * @param string $name Model name
         * @param array|null $args All the Model parameters
         * @param boolean $overwrite Whenever to return new object if altready initialized
         *
         * return mixed
         */
        public static function model($name, $args = null, $overwrite = false)
        {
            if (isset(self::$models[$name]) && self::$models[$name] && !$overwrite) {
                return self::$models[$name];
            }

            $classname = $name .'_model';

            self::$models[$name] = new $classname ($args);

            return self::$models[$name];
        }

        /*
         * Method to load app controller
         *
         * @param string $name Controller name
         * @param array|null $args All the Controller parameters
         * @param boolean $overwrite Whenever to return new object if altready initialized
         *
         * return mixed
         */
        public static function controller($name, $args = null, $overwrite = false)
        {
            if (isset(self::$controllers[$name]) && self::$controllers[$name] && !$overwrite) {
                return self::$controllers[$name];
            }

            $classname = $name .'_controller';

            self::$controllers[$name] = new $classname($args);

            return self::$controllers[$name];
        }

        /*
         * Method to load app view
         *
         * @param string $name View name
         * @param array|null $args Additional arguments
         */
        public static function view($name, $args = null)
        {
            $global_args = EE::get();

            if (is_array($args) && sizeof($args)) {
                $global_args = array_merge($global_args, $args);
            }

            extract($global_args);
            require(self::$dir .'/'. self::APP_VIEWS_DIR .'/'. $name .'_view.php');
        }

        /**
         * Method to correctly format app url
         */
        public static function url($url)
        {
            return self::$routes['base_path'] .'/'. ltrim($url, '/');
        }

        /**
         * Method to return error page
         *
         * @param int $status http status code
         * @param string|null $err_msg error message
         * @param boolean $print_output whenever to print output
         *
         * return string|boolean
         */
        public static function error_page($status = 500, $err_msg = null, $print_output = true)
        {
            $status_msg = array(
                400 => 'Bad Request',
                403 => 'Forbidden',
                404 => 'Not Found',
                500 => 'Internal Server Error',
                503 => 'Service Unavailable',
            );

            if (PHP_SAPI !== 'cli') {
                header('HTTP/1.1 '. $status . $status_msg[$status]);
            }

            $message = ($err_msg ? $err_msg : $status_msg[$status]);

            if ($print_output) {
                echo $message;
                return true;
            }

            return $message;
        }

        /*********** GLOBAL VARIABLE FUNCTIONS ***********/

        /**
         * Sets variable
         * @param mixed $key
         * @param mixed $value
         *
         * return boolean
         */
        public static function set()
        {
            $args = func_get_args();
            $value = array_pop($args);

            $vars = &self::$variables;

            if (sizeof($args)) {
                foreach ($args as $arg) {
                    $vars = &$vars[$arg];
                }

                $vars = $value;

                return true;
            }

            return false;
        }

        /**
         * Retrieve variable
         *
         * @param string|null $name variable name to retrieve
         *
         * return mixed
         */
        public static function get()
        {
            $args = func_get_args();
            $vars = self::$variables;

            foreach ($args as $arg) {
                if (isset($vars[$arg])) {
                    $vars = $vars[$arg];
                } else {
                    $vars = null;
                    break;
                }
            }

            return $vars;
        }

        /**
         * Retrieve variable by reference
         *
         * @param string|null $name variable name to retrieve
         *
         * return mixed
         */
        public static function &get_ref()
        {
            $args = func_get_args();

            $vars = &self::$variables;

            foreach ($args as $arg) {
                $vars = &$vars[$arg];
            }

            return $vars;
        }

        /**
         * Check if variable isset
         *
         * @param string|null $name variable name to retrieve
         *
         * return boolean
         */
        public static function is_set()
        {
            $args = func_get_args();
            $vars = self::$variables;

            foreach ($args as $arg) {
                if (!isset($vars[$arg])) {
                    return false;
                }
                $vars = $vars[$arg];
            }

            return true;
        }


        /**
         * Unsets variable
         *
         * @param string|null $name variable name to retrieve
         *
         * return boolean
         */
        public static function un_set()
        {
            $args = func_get_args();

            if (sizeof($args) > 1) {
                $element = array_pop($args);

                $vars = &self::$variables;

                foreach ($args as $arg) {
                    $vars = &$vars[$arg];
                }

                if (is_array($vars)) {
                    unset($vars[$element]);

                    return true;
                }

                return false;
            } else {
                unset(self::$variables[$args[0]]);

                return true;
            }
        }

        /**
         * Method to redirect user
         *
         * @param string $url where to redirect
         * @param int $status_code status code of redirect
         */
        public static function redirect($url, $status_code = 302)
        {
            header('Location: '. $url, true, $status_code);
            exit;
        }
    }

    spl_autoload_register(array('EE', 'autoload'), true);

    if (file_exists(dirname(__FILE__).'/vendor/autoload.php')) {
        require dirname(__FILE__).'/vendor/autoload.php';
    }
}

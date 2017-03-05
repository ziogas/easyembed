<?php

/**
 * Helper pdo class to return php PDO instance
 *
 * $pdo = db_pdo::load ( 'database_config_name' );
 */
class db_pdo extends PDO
{
    public static $db_instances = array();

    public static function load($database, $force_new_connection = false)
    {
        // Return existing object if connection already exists
        if (!$force_new_connection && isset(self::$db_instances [ $database ])) {
            return self::$db_instances [ $database ];
        }

        self::$db_instances [ $database ] = new db_pdo($database);

        return self::$db_instances [ $database ];
    }

    public function __construct($database)
    {
        if (is_array($database)) {
            $db_config = $database;
        } else {
            $db_config = EE::is_set('_config', 'databases', $database) ? EE::get('_config', 'databases', $database) : false;
        }

        if (!$db_config) {
            trigger_error('No database config of '. $database .' found', E_USER_WARNING);
        }

        if (!isset($db_config [ 'dsn' ])) {
            trigger_error('No database dsn config of '. $database .' found', E_USER_WARNING);
        }

        $extension = substr($db_config [ 'dsn' ], 0, strpos($db_config [ 'dsn' ], ':'));

        if (!in_array($extension, PDO::getAvailableDrivers())) {
            trigger_error('PDO extension of '. $extension .' could not be found', E_USER_WARNING);
        }

        $instance = null;

        $username = isset($db_config [ 'username' ]) ? $db_config [ 'username' ] : null;
        $password = isset($db_config [ 'password' ]) ? $db_config [ 'password' ] : null;
        $params = isset($db_config [ 'params' ]) ? $db_config [ 'params' ] : null;

        try {
            $instance = parent::__construct($db_config [ 'dsn' ], $username, $password, $params);
        } catch (PDOException $e) {
            trigger_error('Could not connect to database '. $database .': '. $e -> getMessage(), E_USER_ERROR);
        }

        return $instance;
    }

    public function prepare($statement, $options = array())
    {
        if (EE::get('env') == 'dev' || isset($_COOKIE [ 'mysql_dev' ])) {
            return new db_query_wrapper($this, $statement, $options);
        }

        return $this -> pdo_prepare($statement, $options);
    }

    public function pdo_prepare($statement, $options = array())
    {
        return parent::prepare($statement, $options);
    }
}

class db_query_wrapper
{
    private $pdo, $statement, $options, $res;

    public function __construct($pdo, $statement, $options = array())
    {
        $this -> pdo = $pdo;
        $this -> statement = $statement;
        $this -> options = $options;

        $this -> res = $this -> pdo -> pdo_prepare($this -> statement, $options);
    }

    public function __call($method, $args)
    {
        if (!EE::is_set('pdo_query_logger')) {
            EE::set('pdo_query_logger', array());
        }

        if ($method == 'execute') {
            EE::set('pdo_query_logger', md5($this -> statement), array( 'statement' => $this -> statement, 'args' => (isset($args [ 0 ]) ? $args [ 0 ] : array()), 'sql' => self::params($this -> statement, $args [ 0 ]) ));
        }

        return call_user_func_array(array( $this -> res, $method ), $args);
    }

    private static function params($string, $data)
    {
        if ($data && sizeof($data)) {
            $indexed = ($data == array_values($data));

            foreach ($data as $k => $v) {
                if (is_string($v)) {
                    $v = "'$v'";
                }

                if ($indexed) {
                    $string = preg_replace('/\?/', $v, $string, 1);
                } else {
                    $string = str_replace($k, $v, $string);
                }
            }
        }

        return $string;
    }
}

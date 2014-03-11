<?php

/**
 * Helper session library
 */
class session
{
    private static $started = false;

    public static function start ()
    {
        if ( !self::$started )
        {
            $session_config = EE::is_set ( '_config', 'session' ) ? EE::get ( '_config', 'session' ) : array ();

            if ( sizeof ( $session_config ) )
            {
                foreach ( $session_config as $key => $value )
                {
                    ini_set ( 'session.'. $key, $value );
                }
            }

            session_start ();

            self::$started = true;
        }
    }

    public static function destroy ()
    {
        $_SESSION = array ();
        return session_destroy ();
    }

    public static function set_message ( $message, $expiration_timestamp = 0 )
    {
        self::start ();

        if ( !isset ( $_SESSION [ '__messages' ] ) )
        {
            $_SESSION [ '__messages' ] = array ();
        }

        $_SESSION [ '__messages' ] [] = array ( $message, $expiration_timestamp );
    }

    public static function get_message ( $remove_after_retrieve = true )
    {
        self::start ();

        $message = null;

        if ( isset ( $_SESSION [ '__messages' ] ) && sizeof ( $_SESSION [ '__messages' ] ) )
        {
            $message = end ( $_SESSION [ '__messages' ] );

            if ( $message [ 1 ] > 0 && $message [ 1 ] < time () )
            {
                array_pop ( $_SESSION [ '__messages' ] );
                $message = null;
            }
            else
            {
                $message = $message [ 0 ];
                if ( $remove_after_retrieve )
                {
                    array_pop ( $_SESSION [ '__messages' ] );
                }
            }
        }

        return $message;
    }
}

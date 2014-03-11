<?php

/**
 * Loads translation from app/translations/LOCALE.php file
 *
 * $phrase = locale::translate ( 'key' );
 */
class locale
{
    private static $translations = null;

    public static function load ( $locale = null )
    {
        self::$translations = array ();

        if ( !$locale && EE::is_set ( '_config', 'locale' ) )
        {
            $locale = EE::get ( '_config', 'locale' );
        }

        if ( $locale )
        {
            $file = EE::get ( '_dir' ) .'/'. EE::APP_TRANSLATIONS_DIR .'/'. $locale .'.php';

            if ( file_exists ( $file ) )
            {
                self::$translations = require ( $file );
                return true;
            }
        }

        return false;
    }

    public static function translate ( $phrase )
    {
        if ( is_null ( self::$translations ) )
        {
            self::load ();
        }

        return isset ( self::$translations [ $phrase ] ) ? self::$translations [ $phrase ] : $phrase;
    }
}

<?php

/*
 * Helper validation library
 */
class validation
{
    public static function is_valid_email ( $email )
    {
        $return = filter_var ( $email, FILTER_VALIDATE_EMAIL ) === false ? false : true;

        if ( !$return )
        {
            $l = mb_strlen ( $email );
            // If email contains unicode
            if ( $l !== strlen ( $email ) )
            {
                $replaced_unicode = self::replace_unicode ( $email );

                // Re-check
                $return = filter_var ( $replaced_unicode, FILTER_VALIDATE_EMAIL ) === false ? false : true;
            }
        }

        return $return;
    }

    public static function is_valid_url ( $url )
    {
        $return = filter_var ( $url, FILTER_VALIDATE_URL ) === false ? false : true;

        if ( !$return )
        {
            $l = mb_strlen ( $url );
            // If email contains unicode
            if ( $l !== strlen ( $url ) )
            {
                $replaced_unicode = self::replace_unicode ( $url );

                // Re-check
                $return = filter_var ( $replaced_unicode, FILTER_VALIDATE_URL ) === false ? false : true;
            }
        }

        return $return;
    }

    private static function replace_unicode ( $str )
    {
        $l = mb_strlen ( $str );
        $s = str_repeat ( ' ', $l );
        // Replacing unicode
        for ( $i = 0; $i < $l; ++$i )
        {
            $ch = mb_substr ( $str, $i, 1 );
            $s [ $i ] = strlen ( $ch ) > 1 ? 'X' : $ch;
        }

        return $replaced_unicode;
    }
}

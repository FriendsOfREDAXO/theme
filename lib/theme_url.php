<?php
/**
 * Provide urls of theme directories
 */

class theme_url extends theme_abstract
{
   /**
     * Return theme url
     *
     * @param string $filename Optional filename
     * @return string Url
     */
    public static function base($filename = '')
    {
        return rex_url::base(self::folder().self::separator($filename));
    }

    /**
     * Return assets url
     *
     * @param string $filename Optional filename
     * @return string Url
     */
    public static function assets($filename = '')
    {
        return self::base('public/assets'.self::separator($filename));
    }
}

<?php
/**
 * Provide real paths of theme directories
 */

class theme_path extends theme_abstract
{
    /**
     * Return theme path
     *
     * @param string $filename Optional filename
     * @return string Path
     */
    public static function base($filename = '')
    {
        return rex_path::base(self::folder().self::separator($filename));
    }

    /**
     * Return lib path
     *
     * @param string $filename Optional filename
     * @return string Path
     */
    public static function lib($filename = '')
    {
        return self::base('private/lib'.self::separator($filename));
    }

    /**
     * Return lang path
     *
     * @param string $filename Optional filename
     * @return string Path
     */
    public static function lang($filename = '')
    {
        return self::base('private/lang'.self::separator($filename));
    }

    /**
     * Return views path
     *
     * @param string $filename Optional filename
     * @return string Path
     */
    public static function views($filename = '')
    {
        return self::base('private/views'.self::separator($filename));
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

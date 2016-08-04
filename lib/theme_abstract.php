<?php
/**
 * Abstract base class for theme path/url providers
 */

abstract class theme_abstract
{
    /**
     * Get addon data
     * @return rex_addon
     */
    protected static function addon()
    {
        return rex_addon::get('theme');
    }

    /**
     * Return theme folder name
     */
    public static function folder()
    {
        return self::addon()->getProperty('theme_folder');
    }

    /**
     * Check for separator and add it, if missing
     *
     * @param $filename
     * @return string
     */
    protected static function separator($filename)
    {
       if ($filename != '' && strpos($filename, '/') !== 0) {
            $filename = '/'.$filename;
       }

       return $filename;
    }
}

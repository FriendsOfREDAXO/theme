<?php
/**
 * Registry class to manage setting arrays and merge them with presets
 */

class theme_setting
{
    /**
     * @var array
     */
    protected static $registry = array();

    /**
     * @param string $key
     * @param array $presets
     */
    public static function setKey($key, array $presets)
    {
        if (!array_key_exists($key, self::$registry)) {
            self::$registry[$key] = array();
        }

        self::$registry[$key] = array_merge(self::$registry[$key], $presets);
    }

    /**
     * @param string $key
     * @param array $presets
     * @return array
     */
    public static function getKey($key, array $presets = array())
    {
        $return = $presets;

        if (array_key_exists($key, self::$registry)) {
            $return = array_merge($return, self::$registry[$key]);
        }

        return $return;
    }

    /**
     * @return array
     */
    public static function getAll()
    {
        return self::$registry;
    }
}

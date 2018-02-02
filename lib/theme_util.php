<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2018 studio ahoi
 */

class theme_util
{
    /**
     * @return string
     */
    public static function getFunctionsPhpMessage()
    {
        $return = '';

        if (file_exists(theme_path::lib('functions.php')) || file_exists(theme_path::lib('Functions.php'))) {
            $return = rex_view::error(rex_i18n::rawMsg('theme_functionsphp_in_lib_folder_is_deprecated'));
        }

        return $return;
    }
}

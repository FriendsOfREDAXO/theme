<?php
/**
 * @internal
 * @author Daniel Weitenauer
 */
class theme_util extends theme_abstract
{
    public static function getFunctionsPhpMessage(): string
    {
        $return = '';

        if (file_exists(theme_path::lib('functions.php')) || file_exists(theme_path::lib('Functions.php'))) {
            $return = rex_view::error(rex_i18n::rawMsg('theme_functionsphp_in_lib_folder_is_deprecated'));
        }

        return $return;
    }
}

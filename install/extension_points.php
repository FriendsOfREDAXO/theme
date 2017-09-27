<?php
rex_extension::register('PACKAGES_INCLUDED', function () {
    if (rex_addon::get('yform')->isAvailable())
    {
        rex_yform::addTemplatePath(theme_path::lib() . DIRECTORY_SEPARATOR . 'ytemplates');
    }
    rex_fragment::addDirectory(theme_path::base() . DIRECTORY_SEPARATOR . 'private' . DIRECTORY_SEPARATOR . 'fragments');
}, 'LATE');
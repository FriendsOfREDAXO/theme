<?php
rex_extension::register('PACKAGES_INCLUDED', function () {
    // Register custom YRewrite scheme
    /*if (rex_addon::get('yrewrite')->isAvailable()) {
        rex_yrewrite::setScheme(new my_rewrite_scheme());
    }*/

    // Register YForm templates
    /*if (rex_addon::get('yform')->isAvailable()) {
        rex_yform::addTemplatePath(theme_path::base('private/ytemplates');
    }*/

    // Register fragment folder
    //rex_fragment::addDirectory(theme_path::base('private/fragments');
}, 'LATE');




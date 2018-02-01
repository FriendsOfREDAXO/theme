<?php
rex_extension::register('PACKAGES_INCLUDED', function () {
    // Register custom yrewrite scheme
    //if (rex_addon::get('yrewrite')->isAvailable()) {
    //    rex_yrewrite::setScheme(new rex_theme_rewrite_scheme());
    //}

    // Register YForm templates
    //if (rex_addon::get('yform')->isAvailable()) {
    //    rex_yform::addTemplatePath(theme_path::base().DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.'ytemplates');
    //}

    // Register fragment folder
    //rex_fragment::addDirectory(theme_path::base().DIRECTORY_SEPARATOR.'private'.DIRECTORY_SEPARATOR.'fragments');
}, 'LATE');




<?php
rex_extension::register('PACKAGES_INCLUDED', function () {
    // Register custom yrewrite scheme
    //if (rex_addon::get('yrewrite')->isAvailable()) {
    //    rex_yrewrite::setScheme(new rex_theme_rewrite_scheme());
    //}
}, 'LATE');



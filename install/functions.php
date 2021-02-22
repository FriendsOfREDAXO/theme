<?php
rex_extension::register('PACKAGES_INCLUDED', function () {
    // Register custom YRewrite scheme
    /*if (rex_addon::get('yrewrite')->isAvailable()) {
        rex_yrewrite::setScheme(new my_rewrite_scheme());
    }*/
}, rex_extension::LATE);




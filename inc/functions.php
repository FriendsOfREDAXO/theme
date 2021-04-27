<?php

/**
 * @internal
 * @return bool
 */
function theme_backwards_compatibility(): bool
{
    return
        rex_string::versionCompare(rex_addon::get('developer')->getVersion(), '3.6.0', '<') ||
        rex_addon::get('theme')->getProperty('force_backwards_compatibility');
}

/**
 * Translate old config settings to new ones
 *
 * @internal
 * @return void
 */
function theme_sync_config()
{
    $addon = rex_addon::get('theme');

    if (!theme_backwards_compatibility() && !$addon->hasConfig('synchronize')) {
        $addon->setConfig('synchronize', false);

        if (
            $addon->getConfig('synchronize_actions') == true ||
            $addon->getConfig('synchronize_modules') == true ||
            $addon->getConfig('synchronize_templates') == true ||
            $addon->getConfig('synchronize_yformemails') == true
        ) {
            $addon->setConfig('synchronize', true);

            // Set developer synchronizing actions according to theme settings
            if ($addon->getConfig('synchronize_templates')) {
                rex_addon::get('developer')->setConfig('templates', true);
            }
            if ($addon->getConfig('synchronize_modules')) {
                rex_addon::get('developer')->setConfig('modules', true);
            }
            if ($addon->getConfig('synchronize_actions')) {
                rex_addon::get('developer')->setConfig('actions', true);
            }
            if ($addon->getConfig('synchronize_yformemails')) {
                rex_addon::get('developer')->setConfig('yform_email', true);
            }
        }

        $addon->removeConfig('synchronize_actions');
        $addon->removeConfig('synchronize_modules');
        $addon->removeConfig('synchronize_templates');
        $addon->removeConfig('synchronize_yformemails');
    }
}

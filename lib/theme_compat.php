<?php
/**
 * @internal
 * @author Daniel Weitenauer
 */
class theme_compat extends theme_abstract
{
    public static function isBackwardsCompatible(): bool
    {
        return
            (rex_addon::get('developer')->isAvailable() &&
            rex_string::versionCompare(rex_addon::get('developer')->getVersion(), '3.6.0', '<')) ||
            self::addon()->getProperty('force_backwards_compatibility');
    }

    /**
     * Translate old config settings to new ones
     */
    public static function syncConfig()
    {
        $addon = self::addon();

        if (!self::isBackwardsCompatible() && !$addon->hasConfig('synchronize')) {
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
}

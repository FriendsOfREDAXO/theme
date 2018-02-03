<?php
/**
 * Theme
 *
 * @var rex_addon $this
 */

// Load theme languages
rex_i18n::addDirectory(theme_path::lang());

// Autoload theme lib
rex_autoload::addDirectory(theme_path::lib());

// Include files
if (is_dir(theme_path::inc())) {
    foreach (glob(theme_path::inc('*.php')) as $file) {
        include_once ($file);
    }
}

// Deprecated fallback warning
rex_extension::register('PAGE_STRUCTURE_HEADER', function() {
    return theme_util::getFunctionsPhpMessage();
});

rex_extension::register('PACKAGES_INCLUDED', function () {
    // Register fragment folder
    rex_fragment::addDirectory(theme_path::fragments());
    // Register YForm templates
    if (rex_addon::get('yform')->isAvailable()) {
        rex_yform::addTemplatePath(theme_path::ytemplates());
    }
}, rex_extension::LATE);

// Include backend assets
if (rex::isBackend() && $this->getConfig('include_be_files')) {
    rex_extension::register('PACKAGES_INCLUDED', 'theme_add_backend_assets', rex_extension::LATE);
}

// Configure developer
if (rex_addon::get('developer')->isAvailable()) {
    // Add JS to deactivate developers synchronize checkboxes
    rex_extension::register('PAGE_HEADER', 'theme_deactivate_developer_checkboxes', rex_extension::LATE, [
        'addon' => $this,
    ]);
    // Register own theme paths
    rex_extension::register('DEVELOPER_MANAGER_START', array('theme_manager', 'start'), rex_extension::NORMAL, [
        'theme_folder' => $this->getProperty('theme_folder'),
        'synchronize_actions' => $this->getConfig('synchronize_actions'),
        'synchronize_modules' => $this->getConfig('synchronize_modules'),
        'synchronize_templates' => $this->getConfig('synchronize_templates'),
    ]);
}

/**
 * Add JS to deactivate developers synchronize checkboxes
 * EP: PAGE_HEADER
 *
 * @param rex_extension_point $ep
 * @return mixed|string
 */
function theme_deactivate_developer_checkboxes(rex_extension_point $ep)
{
    /** @var rex_addon $addon */
    $addon = $ep->getParam('addon');
    $subject = $ep->getSubject();

    $subject .= '
    <!-- theme -->
    <script type="text/javascript">
        jQuery("document").ready(function() {
            if (jQuery("body").attr("id") == "rex-page-developer-settings") {
                var $templates = jQuery("#rex-developer-templates");
                var $modules = jQuery("#rex-developer-modules");
                var $actions = jQuery("#rex-developer-actions");
                
                if ('.($addon->getConfig('synchronize_templates') ? '1' : '0').') {
                    $templates.prop("disabled", "disabled").parent().append(" ['.$addon->i18n('set_by_theme').']");
                }
                if ('.($addon->getConfig('synchronize_modules') ? '1' : '0').') {
                    $modules.prop("disabled", "disabled").parent().append(" ['.$addon->i18n('set_by_theme').']");
                }
                if ('.($addon->getConfig('synchronize_actions') ? '1' : '0').') {
                    $actions.prop("disabled", "disabled").parent().append(" ['.$addon->i18n('set_by_theme').']");
                }
            }
        });
    </script>
    <!-- end theme -->
    ';

    return $subject;
}

/**
 * Add backend assets
 * EP: PACKAGES_INCLUDED
 *
 * @param rex_extension_point $ep
 */
function theme_add_backend_assets(rex_extension_point $ep)
{
    if (file_exists(theme_path::assets('backend/backend.css'))) {
        rex_view::addCssFile(theme_url::assets('backend/backend.css'));
    }
    if (file_exists(theme_path::assets('backend/backend.js'))) {
        rex_view::addJsFile(theme_url::assets('backend/backend.js'));
    }
}

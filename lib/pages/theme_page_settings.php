<?php
/**
 * Install theme folder
 */

class theme_page_settings extends theme_abstract
{
    /**
     * @return string
     */
    public static function getFormPost()
    {
        $addon = self::addon();

        $message = '';

        // Process form data
        if (rex_post('submit', 'boolean')) {
            $addon->setConfig(rex_post('config', [
                ['include_be_files', 'bool'],
                ['synchronize_actions', 'bool'],
                ['synchronize_modules', 'bool'],
                ['synchronize_templates', 'bool'],
            ]));

            $message = rex_view::success($addon->i18n('saved'));
        }

        return $message;
    }

    /**
     * @return string
     */
    public static function getForm()
    {
        $addon = self::addon();

        // Checkboxes
        $checkbox_elements = [];
        if (rex_addon::get('developer')->isAvailable()) {
            $checkbox_elements[] = [
                'label' => '<label for="theme-synchronize-templates">'.$addon->i18n('synchronize_templates').'</label>',
                'field' => '<input type="checkbox" id="theme-synchronize-templates" name="config[synchronize_templates]" value="1" '.($addon->getConfig('synchronize_templates') ? ' checked="checked"' : '').' />',
            ];
            $checkbox_elements[] = [
                'label' => '<label for="theme-synchronize-modules">'.$addon->i18n('synchronize_modules').'</label>',
                'field' => '<input type="checkbox" id="theme-synchronize-modules" name="config[synchronize_modules]" value="1" '.($addon->getConfig('synchronize_modules') ? ' checked="checked"' : '').' />',
            ];
            $checkbox_elements[] = [
                'label' => '<label for="theme-synchronize-actions">'.$addon->i18n('synchronize_actions').'</label>',
                'field' => '<input type="checkbox" id="theme-synchronize-actions" name="config[synchronize_actions]" value="1" '.($addon->getConfig('synchronize_actions') ? ' checked="checked"' : '').' />'
            ];
        }
        $checkbox_elements[] = [
            'label' => '<label for="theme-include-be-files">'.$addon->i18n('include_be_files').'</label>',
            'field' => '<input type="checkbox" id="theme-include-be-files" name="config[include_be_files]" value="1" '.($addon->getConfig('include_be_files') ? ' checked="checked"' : '').' />',
        ];

        $fragment = new rex_fragment();
        $fragment->setVar('elements', $checkbox_elements, false);
        $checkboxes = $fragment->parse('core/form/checkbox.php');

        // Submit
        $submit_elements = [];
        $submit_elements[] = [
            'field' => '<button class="btn btn-save rex-form-aligned" type="submit" name="submit" value="1" '.rex::getAccesskey($addon->i18n('submit'), 'save').'>'.$addon->i18n('save').'</button>',
        ];

        $fragment = new rex_fragment();
        $fragment->setVar('flush', true);
        $fragment->setVar('elements', $submit_elements, false);
        $submit = $fragment->parse('core/form/submit.php');

        // Options
        $options = '
            <div class="btn-group btn-group-xs">
                <a href="'.rex_url::currentBackendPage().'&theme_install_folders=true" class="btn btn-default">'.rex_i18n::msg('theme_install_folders').'</a>
            </div>
        ';

        // Form
        $fragment = new rex_fragment();
        $fragment->setVar('class', 'edit');
        $fragment->setVar('title', $addon->i18n('settings'));
        $fragment->setVar('options', $options, false);
        $fragment->setVar('body', $checkboxes, false);
        $fragment->setVar('buttons', $submit, false);
        $form = $fragment->parse('core/page/section.php');

        return $form;
    }

    /**
     * @return string
     */
    public static function install()
    {
        $addon = self::addon();

        // Setup folder for all website related assets and settings
        $theme_folder = $addon->getProperty('theme_folder');

        $message = '';

        if (rex_get('theme_install_folders', 'boolean')) {
            // Main folder
            $status = rex_dir::create(rex_path::base($theme_folder));

            // Public folders
            if ($status) {
                $status = rex_dir::create(rex_path::base($theme_folder.'/public/assets'));
            }

            // Public files
            if ($status && !file_exists(rex_path::base($theme_folder.'/public/assets/backend/backend.js'))) {
                $status = rex_file::copy($addon->getPath('install/backend.js'), rex_path::base($theme_folder.'/public/assets/backend/backend.js'));
            }
            if ($status && !file_exists(rex_path::base($theme_folder.'/public/assets/backend/backend.css'))) {
                $status = rex_file::copy($addon->getPath('install/backend.css'), rex_path::base($theme_folder.'/public/assets/backend/backend.css'));
            }

            // Private folders
            if ($status) {
                $status = rex_dir::create(rex_path::base($theme_folder.'/private'));
            }
            if ($status) {
                $status = rex_dir::create(rex_path::base($theme_folder.'/private/lang'));
            }
            if ($status) {
                $status = rex_dir::create(rex_path::base($theme_folder.'/private/lib'));
            }
            if ($status) {
                $status = rex_dir::create(rex_path::base($theme_folder.'/private/redaxo'));
            }
            if ($status) {
                $status = rex_dir::create(rex_path::base($theme_folder.'/private/redaxo/actions'));
            }
            if ($status) {
                $status = rex_dir::create(rex_path::base($theme_folder.'/private/redaxo/modules'));
            }
            if ($status) {
                $status = rex_dir::create(rex_path::base($theme_folder.'/private/redaxo/templates'));
            }
            if ($status) {
                $status = rex_dir::create(rex_path::base($theme_folder.'/private/views'));
            }

            // Private files
            if ($status && !file_exists(rex_path::base($theme_folder.'/private/.htaccess'))) {
                $status = rex_file::copy($addon->getPath('install/_htaccess'), rex_path::base($theme_folder.'/private/.htaccess'));
            }
            if ($status && !file_exists(rex_path::base($theme_folder.'/private/lib/functions.php'))) {
                $status = rex_file::copy($addon->getPath('install/functions.php'), rex_path::base($theme_folder.'/private/lib/functions.php'));
            }

            // Status
            if ($status) {
                $message = rex_view::success(rex_i18n::msg('theme_folders_installed'));
            } else {
                $message = rex_view::error(rex_i18n::msg('theme_folders_not_installed'));
            }
        }

        return $message;
    }
}

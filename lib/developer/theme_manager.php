<?php
/**
 * Extend developer addon with own theme path
 *
 * @author Daniel Weitenauer
 */

class theme_manager
{
    public static function start(rex_extension_point $ep)
    {
        // Unset developer synchronizing actions to prevent perpetual overwriting
        if ($ep->getParam('synchronize_templates')) {
            rex_addon::get('developer')->setConfig('templates', false);
        }
        if ($ep->getParam('synchronize_modules')) {
            rex_addon::get('developer')->setConfig('modules', false);
        }
        if ($ep->getParam('synchronize_actions')) {
            rex_addon::get('developer')->setConfig('actions', false);
        }

        // Register own directories
        self::register($ep);
    }

    /**
     * Matches rex_developer_manager::registerDefault,
     * but changes synchronize paths to theme folder
     *
     * @param rex_extension_point $ep
     */
    public static function register(rex_extension_point $ep)
    {
        $theme_folder = $ep->getParam('theme_folder');

        $page = rex_be_controller::getCurrentPage();
        $function = rex_request('function', 'string', '');
        $save = rex_request('save', 'string', '');

        if ($ep->getParam('synchronize_templates')) {
            $synchronizer = new theme_synchronizer(
                $theme_folder.'/private/redaxo/templates',
                rex::getTable('template'),
                array('content' => 'template.php'),
                array('active' => 'boolean', 'attributes' => 'json')
            );
            $synchronizer->setEditedCallback(function (rex_developer_synchronizer_item $item) {
                $template = new rex_template($item->getId());
                $template->deleteCache();
            });

            rex_developer_manager::register(
                $synchronizer,
                $page == 'templates' && ((($function == 'add' || $function == 'edit') && $save == 'ja') || $function == 'delete')
            );
        }

        if ($ep->getParam('synchronize_modules')) {
            $synchronizer = new theme_synchronizer(
                $theme_folder.'/private/redaxo/modules',
                rex::getTable('module'),
                array('input' => 'input.php', 'output' => 'output.php')
            );
            $synchronizer->setEditedCallback(function (rex_developer_synchronizer_item $item) {
                $sql = rex_sql::factory();
                $sql->setQuery('
                        SELECT     DISTINCT(article.id)
                        FROM       ' . rex::getTable('article') . ' article
                        LEFT JOIN  ' . rex::getTable('article_slice') . ' slice
                        ON         article.id = slice.article_id
                        WHERE      slice.module_id=' . $item->getId()
                );
                for ($i = 0, $rows = $sql->getRows(); $i < $rows; ++$i) {
                    rex_article_cache::delete($sql->getValue('article.id'));
                    $sql->next();
                }
            });

            rex_developer_manager::register(
                $synchronizer,
                $page == 'modules/modules' && ((($function == 'add' || $function == 'edit') && $save == '1') || $function == 'delete')
            );
        }

        if ($ep->getParam('synchronize_actions')) {
            $synchronizer = new theme_synchronizer(
                $theme_folder.'/private/redaxo/actions',
                rex::getTable('action'),
                array('preview' => 'preview.php', 'presave' => 'presave.php', 'postsave' => 'postsave.php'),
                array('previewmode' => 'int', 'presavemode' => 'int', 'postsavemode' => 'int')
            );

            rex_developer_manager::register(
                $synchronizer,
                $page == 'modules/actions' && ((($function == 'add' || $function == 'edit') && $save == '1') || $function == 'delete')
            );
        }
    }
}

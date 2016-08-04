<?php
/**
 * Theme
 *
 * @var rex_addon $this
 */
?>

<?= rex_view::title($this->i18n('name')); ?>

<?= theme_page_settings::getFormPost(); ?>
<?= theme_page_settings::install(); ?>

<form action="<?=rex_url::currentBackendPage();?>" method="post">
    <?= theme_page_settings::getForm(); ?>
</form>

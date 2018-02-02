<?php
/**
 * Theme
 *
 * @var rex_addon $this
 */
?>

<?= theme_page_settings::getFormPost(); ?>
<?= theme_page_settings::install(); ?>
<?= theme_util::getFunctionsPhpMessage();?>

<form action="<?=rex_url::currentBackendPage();?>" method="post">
    <?= theme_page_settings::getForm(); ?>
</form>

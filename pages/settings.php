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
    <?php if (theme_compat::isBackwardsCompatible()): ?>
        <?= theme_page_settings::getForm(); ?>
    <?php else: ?>
        <?= theme_page_settings::getSettingsForm(); ?>
    <?php endif; ?>
</form>

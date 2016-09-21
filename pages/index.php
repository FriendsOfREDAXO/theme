<?php
/**
 * Theme
 *
 * @var rex_addon $this
 */
echo rex_view::title(rex_i18n::msg('name'));

if($subpage = rex_be_controller::getCurrentPagePart(2))
{
    include rex_be_controller::getCurrentPageObject()->getSubPath();
}

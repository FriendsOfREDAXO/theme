<?php
/**
 * @var rex_addon $this
 */

$file = rex_file::get($this->getPath('README.md'));

$parsedown = new Parsedown();
$content = $parsedown->text($file);

$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('info'));
$fragment->setVar('body', $content, false);

echo $fragment->parse('core/page/section.php');

<?php
/**
 * Theme
 *
 * @var rex_addon $this
 */

// Config
if (!$this->hasConfig()) {
    $this->setConfig([
        'include_be_files' => false,
        'synchronize_actions' => false,
        'synchronize_modules' => false,
        'synchronize_templates' => false,
        'synchronize_yformemails' => false,
    ]);
}

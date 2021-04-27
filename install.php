<?php
/**
 * Theme
 *
 * @var rex_addon $this
 */
require_once('inc/functions.php');

// Config
if (!$this->hasConfig()) {
    if (theme_backwards_compatibility()) {
        $this->setConfig([
            'include_be_files' => false,
            'synchronize_actions' => false,
            'synchronize_modules' => false,
            'synchronize_templates' => false,
            'synchronize_yformemails' => false,
        ]);
    } else {
        $this->setConfig([
            'include_be_files' => false,
            'synchronize' => false,
        ]);
    }
}

<?php
/**
 * Theme
 *
 * @var rex_addon $this
 */

// Config
if (!$this->hasConfig()) {
    if (theme_compat::isBackwardsCompatible()) {
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

// Ensure that all updates are also executed on re-install
include ('update.php');

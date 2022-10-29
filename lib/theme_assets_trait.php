<?php

/**
 * Helper methods for asset handling
 *
 * @author Daniel Weitenauer
 */
trait theme_assets_trait
{
    private function getScriptTag(string $key, string $file, array $attributes, string $cache_buster = ''): string
    {
        if ($this->isAdmin()) {
            $attributes['class'] = (isset($attributes['class']) ? $attributes['class'].' ' : '').'script--'.$key;
        }

        $attributes['src'] = $this->stripDots($file).$this->getCacheBuster($file, $cache_buster);

        return '<script'.rex_string::buildAttributes($attributes).'></script>'.PHP_EOL;
    }

    private function getLinkTag(string $key, string $file, array $attributes, string $cache_buster = ''): string
    {
        if ($this->isAdmin()) {
            $attributes['class'] = (isset($attributes['class']) ? $attributes['class'].' ' : '').'style--'.$key;
        }

        $attributes['href'] = $this->stripDots($file).$this->getCacheBuster($file, $cache_buster);

        $attributes['rel'] = $attributes['rel'] ?? 'stylesheet';
        $attributes['type'] = $attributes['type'] ?? 'text/css';

        return '<link'.rex_string::buildAttributes($attributes).' />'.PHP_EOL;
    }

    private function stripDots(string $url): string
    {
        return ltrim($url, '.');
    }

    private function getCacheBuster(string $filename, string $cache_buster): string
    {
        if (!$cache_buster) {
            return '';
        }

        if ($cache_buster === 'time') {
            $buster = 'c='.time();
        } elseif ($cache_buster === 'filetime') {
            $path = \rex_path::base(ltrim($filename, '/'));
            $buster = 't='.filemtime($path);
        } else {
            $buster = 'v='.$cache_buster;
        }

        return (strpos($filename, '?') !== false ? '&' : '?').$buster;
    }

    private function isAdmin(): bool
    {
        $user = rex::isBackend() ? rex::getUser() : rex_backend_login::createUser();

        return $user instanceof rex_user && $user->isAdmin();
    }
}

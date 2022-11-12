<?php

/**
 * Helper methods for asset handling
 *
 * @author Daniel Weitenauer
 */
trait theme_assets_trait
{
    /**
     * Builds a <script> tag
     *
     * @param string $key File key, used for information purposes only
     * @param string $file File name
     * @param array $attributes Additional attributes
     * @param string $cache_buster The cache buster type to be appended to the file name, pass empty string to deactivate
     *
     * @return string
     */
    private function getScriptTag(string $key, string $file, array $attributes, string $cache_buster = ''): string
    {
        if ($this->isAdmin()) {
            $attributes['class'] = (isset($attributes['class']) ? $attributes['class'].' ' : '').'script--'.$key;
        }

        $attributes['src'] = $this->stripDots($file).$this->getCacheBuster($file, $cache_buster);

        return '<script'.rex_string::buildAttributes($attributes).'></script>'.PHP_EOL;
    }

    /**
     * Builds a <link> tag
     *
     * @param string $key File key, used for information purposes only
     * @param string $file File name
     * @param array $attributes Additional attributes
     * @param string $cache_buster The cache buster type to be appended to the file name, pass empty string to deactivate
     *
     * @return string
     */
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

    /**
     * Removes dots from the beginning of an url
     *
     * @param string $url
     *
     * @return string
     */
    private function stripDots(string $url): string
    {
        return ltrim($url, '.');
    }

    /**
     * Builds a cache buster string
     *
     * @param string $filename File name
     * @param string $cache_buster Cache buster type
     *    - "time": Appends the current system time: "filename?t=1600000000". Forces a server load on every page request. Use in development only.
     *    - "filedate": Appends the files' update date: "filename?f=1600000000". Forces a server load if the file was changed.
     *    - Custom string (e.g. "1.0.0"): Appends a custom string to the "filename?v=1.0.0". Forces a server load if the string was changed.
     *
     * @return string
     */
    private function getCacheBuster(string $filename, string $cache_buster): string
    {
        if (!$cache_buster) {
            return '';
        }

        if ($cache_buster === 'time') {
            $buster = 't='.time();
        } elseif ($cache_buster === 'filetime') {
            $path = \rex_path::base(ltrim($filename, '/'));
            $buster = 'f='.filemtime($path);
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

<?php

/**
 * Adds support for FriendsOfREDAXO/minify to theme_assets,
 * implements actions minify, minify_css, minify_js
 *
 * @author Daniel Weitenauer
 */
class theme_minify
{
    use theme_assets_trait;

    public static function init(): void
    {
        if (!rex_addon::get('minify')->isAvailable()) {
            throw new rex_exception('Addon FriendsOfREDAXO/minify is not available.');
        }

        $theme_minify = new self();
        rex_extension::register('THEME_ASSETS_CSS', [$theme_minify, 'css']);
        rex_extension::register('THEME_ASSETS_JS', [$theme_minify, 'js']);
    }

    protected function __construct() {}

    /**
     * Minify CSS
     *
     * @param rex_extension_point $ep
     *
     * @return string
     */
    public function css(rex_extension_point $ep): string
    {
        // Subject is usually empty
        // If there are multiple extensions registered, it may happen that processed data is passed.
        // In that case minifying is skipped, to prevent compatibility issues
        $return = $ep->getSubject();
        if ($return) {
            return $return;
        }

        // Available parameters
        $id = $ep->getParam('id');
        $action = $ep->getParam('action');
        $data = $ep->getParam('data');
        $cache_buster = $ep->getParam('cache_buster');

        // Minify only if requested by action minify or minify_css
        if ($action !== 'minify' && $action !== 'minify_css') {
            return '';
        }

        $set_id = $id.'--css-';

        // Combine and minify
        $minify = new minify();

        $minify_sets = [];
        foreach ($data as $css_key => $css) {
            // Do not minify external files
            if (strpos($css['data'], 'http') === 0 || strpos($css['data'], '//') === 0) {
                $return .= $this->getLinkTag($css_key, $css['data'], $css['attributes'], $cache_buster);
            }
            // Collect data sets
            else {
                $media = $css['attributes']['media'];
                $minify_sets[$media][] = $css['data'];
                $minify->addFile($css['data'], $set_id.$media);
            }
        }

        foreach ($minify_sets as $media => $set) {
            $return .= $this->getLinkTag($set_id.$media, $minify->minify('css', $set_id.$media), ['media' => $media]);
        }

        return $return;
    }

    /**
     * Minify JavaScript
     *
     * @param rex_extension_point $ep
     *
     * @return string
     */
    public function js(rex_extension_point $ep): string
    {
        // Subject is usually empty
        // If there are multiple extensions registered, it may happen that processed data is passed.
        // In that case minifying is skipped, to prevent compatibility issues
        $return = $ep->getSubject();
        if ($return) {
            return $return;
        }

        // Available parameters
        $id = $ep->getParam('id');
        $action = $ep->getParam('action');
        $data = $ep->getParam('data');
        $cache_buster = $ep->getParam('cache_buster');
        $header = $ep->getParam('header');

        // Minify only if requested by action minify or minify_js
        if ($action !== 'minify' && $action !== 'minify_js') {
            return '';
        }

        // The set id is part of the minified files' name: bundled.{$set_id}.js
        $set_id = $id.'--js-'.($header ? 'header' : 'footer');

        // Combine and minify files
        $minify = new minify();

        foreach ($data as $js) {
            $minify->addFile($js['script'], $set_id);
        }

        $return .= $this->getScriptTag($set_id, $minify->minify('js', $set_id), $js['attributes']);

        return $return;
    }
}

<?php

/**
 * REX_THEME_ASSETS[name=instance type=css]
 * REX_THEME_ASSETS[name=instance type=css_inline]
 * REX_THEME_ASSETS[name=instance type=js header=0]
 * REX_THEME_ASSETS[name=instance type=js_inline header=0]
 * REX_THEME_ASSETS[name=instance type=html header=0]
 *
 * @author Daniel Weitenauer
 */
class rex_var_theme_assets extends rex_var
{
    protected function getOutput(): string
    {
        $name = $this->getArg('name', 'default', true);
        $type = $this->getArg('type', '', true);
        $header = $this->getArg('header', 0);

        switch ($type) {
            // REX_THEME_ASSETS[name=instance type=css]
            case 'css':
                $find = 'REX_AHOI_ASSETS_REPLACE_CSS['.$name.']';
                break;

            // REX_THEME_ASSETS[name=instance type=css_inline]
            case 'css_inline':
                $find = 'REX_AHOI_ASSETS_REPLACE_CSS_INLINE['.$name.']';
                break;

            // REX_THEME_ASSETS[name=instance type=js header=0]
            case 'js':
                if ($header) {
                    $find = 'REX_AHOI_ASSETS_REPLACE_JS_HEADER['.$name.']';
                } else {
                    $find = 'REX_AHOI_ASSETS_REPLACE_JS_FOOTER['.$name.']';
                }
                break;

            // REX_THEME_ASSETS[name=instance type=js_inline header=0]
            case 'js_inline':
                if ($header) {
                    $find = 'REX_AHOI_ASSETS_REPLACE_JS_HEADER_INLINE['.$name.']';
                } else {
                    $find = 'REX_AHOI_ASSETS_REPLACE_JS_FOOTER_INLINE['.$name.']';
                }
                break;

            // REX_THEME_ASSETS[name=instance type=html header=0]
            case 'html':
                if ($header) {
                    $find = 'REX_AHOI_ASSETS_REPLACE_HTML_HEADER['.$name.']';
                } else {
                    $find = 'REX_AHOI_ASSETS_REPLACE_HTML_FOOTER['.$name.']';
                }
                break;

            default:
                $find = '';
        }

        return '"'.$find.'", '.self::class.'::replace("'.$find.'", "'.$type.'", "'.$name.'", "'.$header.'")';
    }

    public static function replace(string $find, string $type, string $name, string $header): string
    {
        // EP must be called early to work with addon Sprog
        rex_extension::register('OUTPUT_FILTER', static function(rex_extension_point $ep) {
            $find = $ep->getParam('find');
            $type = $ep->getParam('type');
            $name = $ep->getParam('name');
            $header = (bool) $ep->getParam('header');

            $replace = '';
            $assets = theme_assets::getInstance($name);

            if ($assets instanceof theme_assets) {
                switch ($type) {
                    // REX_THEME_ASSETS[name=instance type=css]
                    case 'css':
                        $replace = $assets->getCss();
                        break;

                    // REX_THEME_ASSETS[name=instance type=css_inline]
                    case 'css_inline':
                        $replace = $assets->getCssInline();
                        break;

                    // REX_THEME_ASSETS[name=instance type=js header=0]
                    case 'js':
                        $replace = $assets->getJs($header);
                        break;

                    // REX_THEME_ASSETS[name=instance type=js_inline header=0]
                    case 'js_inline':
                        $replace = $assets->getJsInline($header);
                        break;

                    // REX_THEME_ASSETS[name=instance type=html header=0]
                    case 'html':
                        $replace = $assets->getHtml($header);
                        break;
                }
            }

            $ep->setSubject(str_replace($find, $replace, $ep->getSubject()));
        }, rex_extension::EARLY, [
            'find' => $find,
            'type' => $type,
            'name' => $name,
            'header' => $header,
        ]);

        return '';
    }
}

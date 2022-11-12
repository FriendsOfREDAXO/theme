<?php

/**
 * REX_THEME_ASSETS[id=instance type=css]
 * REX_THEME_ASSETS[id=instance type=css_inline]
 * REX_THEME_ASSETS[id=instance type=js header=0/1]
 * REX_THEME_ASSETS[id=instance type=js_inline header=0/1]
 * REX_THEME_ASSETS[id=instance type=html header=0/1]
 *
 * @author Daniel Weitenauer
 */
class rex_var_theme_assets extends rex_var
{
    protected function getOutput(): string
    {
        $id = $this->getArg('id', 'default', true);
        $type = $this->getArg('type', '', true);
        $header = $this->getArg('header', 0);

        switch ($type) {
            // REX_THEME_ASSETS[id=instance type=css]
            case 'css':
                $find = 'REX_AHOI_ASSETS_REPLACE_CSS['.$id.']';
                break;

            // REX_THEME_ASSETS[id=instance type=css_inline]
            case 'css_inline':
                $find = 'REX_AHOI_ASSETS_REPLACE_CSS_INLINE['.$id.']';
                break;

            // REX_THEME_ASSETS[id=instance type=js header=0/1]
            case 'js':
                if ($header) {
                    $find = 'REX_AHOI_ASSETS_REPLACE_JS_HEADER['.$id.']';
                } else {
                    $find = 'REX_AHOI_ASSETS_REPLACE_JS_FOOTER['.$id.']';
                }
                break;

            // REX_THEME_ASSETS[id=instance type=js_inline header=0/1]
            case 'js_inline':
                if ($header) {
                    $find = 'REX_AHOI_ASSETS_REPLACE_JS_HEADER_INLINE['.$id.']';
                } else {
                    $find = 'REX_AHOI_ASSETS_REPLACE_JS_FOOTER_INLINE['.$id.']';
                }
                break;

            // REX_THEME_ASSETS[id=instance type=html header=0/1]
            case 'html':
                if ($header) {
                    $find = 'REX_AHOI_ASSETS_REPLACE_HTML_HEADER['.$id.']';
                } else {
                    $find = 'REX_AHOI_ASSETS_REPLACE_HTML_FOOTER['.$id.']';
                }
                break;

            default:
                $find = '';
        }

        return '"'.$find.'", '.self::class.'::replace("'.$find.'", "'.$type.'", "'.$id.'", "'.$header.'")';
    }

    public static function replace(string $find, string $type, string $id, string $header): string
    {
        // EP must be called early to work with addon Sprog
        rex_extension::register('OUTPUT_FILTER', static function(rex_extension_point $ep) {
            $find = $ep->getParam('find');
            $type = $ep->getParam('type');
            $id = $ep->getParam('id');
            $header = $ep->getParam('header');

            $assets = theme_assets::getInstance($id);

            switch ($type) {
                // REX_THEME_ASSETS[id=instance type=css]
                case 'css':
                    $replace = $assets->getCss();
                    break;

                // REX_THEME_ASSETS[id=instance type=css_inline]
                case 'css_inline':
                    $replace = $assets->getCssInline();
                    break;

                // REX_THEME_ASSETS[id=instance type=js header=0/1]
                case 'js':
                    $replace = $assets->getJs($header);
                    break;

                // REX_THEME_ASSETS[id=instance type=js_inline header=0/1]
                case 'js_inline':
                    $replace = $assets->getJsInline($header);
                    break;

                // REX_THEME_ASSETS[id=instance type=html header=0/1]
                case 'html':
                    $replace = $assets->getHtml($header);
                    break;

                default:
                    $replace = '';
            }

            $ep->setSubject(str_replace($find, $replace, $ep->getSubject()));
        }, rex_extension::EARLY, [
            'find' => $find,
            'type' => $type,
            'id' => $id,
            'header' => $header,
        ]);

        return '';
    }
}

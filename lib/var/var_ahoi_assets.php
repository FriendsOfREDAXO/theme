<?php
/**
 * REX_THEME_ASSETS[instance=name type=css inline=0 minify=0 buster=0]
 * REX_THEME_ASSETS[instance=name type=js header=0 inline=0 minify=0 buster=0]
 * REX_THEME_ASSETS[instance=name type=html header=0]
 *
 * @author Daniel Weitenauer
 */

class rex_var_theme_assets extends rex_var
{
    protected function getOutput(): string
    {
        $type = $this->getArg('type', '', true);
        $name = $this->getArg('instance', 'default', true);
        $header = $this->getArg('header', 0);
        $inline = $this->getArg('inline', 0);
        $minify = $this->getArg('minify', -1);
        $buster = $this->getArg('buster', -1);

        switch ($type) {
            // REX_THEME_ASSETS[type=css inline=0 minify=0 buster=0]
            case 'css':
                if ($inline) {
                    $find = 'REX_THEME_ASSETS_REPLACE_CSS_INLINE['.$name.']';
                } else {
                    $find = 'REX_THEME_ASSETS_REPLACE_CSS['.$name.']';
                }
                break;

            // REX_THEME_ASSETS[type=js header=0 inline=0 minify=0 buster=0]
            case 'js':
                if ($header) {
                    if ($inline) {
                        $find = 'REX_THEME_ASSETS_REPLACE_JS_HEADER_INLINE['.$name.']';
                    } else {
                        $find = 'REX_THEME_ASSETS_REPLACE_JS_HEADER['.$name.']';
                    }
                } else {
                    if ($inline) {
                        $find = 'REX_THEME_ASSETS_REPLACE_JS_FOOTER_INLINE['.$name.']';
                    } else {
                        $find = 'REX_THEME_ASSETS_REPLACE_JS_FOOTER['.$name.']';
                    }
                }
                break;

            // REX_THEME_ASSETS[type=html header=0]
            case 'html':
                if ($header) {
                    $find = 'REX_THEME_ASSETS_REPLACE_HTML_HEADER['.$name.']';
                } else {
                    $find = 'REX_THEME_ASSETS_REPLACE_HTML_FOOTER['.$name.']';
                }
                break;

            default:
                $find = '';
        }

        return '"'.$find.'", '.self::class.'::replace("'.$find.'", "'.$type.'", "'.$name.'", "'.$header.'", "'.$inline.'", "'.$minify.'", "'.$buster.'")';
    }

    public static function replace(string $find, string $type, string $name, string $header, string $inline, string $minify, string $buster): string
    {
        // EP call must be early to work with addon Sprog
        rex_extension::register('OUTPUT_FILTER', function(\rex_extension_point $ep) {
            $find = $ep->getParam('find');
            $type = $ep->getParam('type');
            $name = $ep->getParam('name');
            $header = $ep->getParam('header');
            $inline = $ep->getParam('inline');
            $minify = $ep->getParam('minify');
            $buster = $ep->getParam('buster');

            $replace = '';
            $assets = theme_assets::getInstance($name);

            if ($assets) {
                if ($buster > -1) {
                    $assets->setCacheBuster($buster);
                }
                if ((int) $minify < 0) {
                    $minify = null;
                }

                switch ($type) {
                    // REX_THEME_ASSETS[type=css inline=0 minify=0 buster=1]
                    case 'css':
                        if ($inline) {
                            $replace = $assets->getCssInline();
                        } else {
                            $replace = $assets->getCss($name);
                        }
                        break;

                    // REX_THEME_ASSETS[type=js header=0 inline=0 minify=0 buster=1]
                    case 'js':
                        if ($inline) {
                            $replace = $assets->getJsInline($header);
                        } else {
                            $replace = $assets->getJs($header, $name);
                        }
                        break;

                    // REX_THEME_ASSETS[type=html header=0]
                    case 'html':
                        $replace = $assets->getHtml($header);
                        break;

                    default:
                        // void
                }
            }

            $ep->setSubject(str_replace($find, $replace, $ep->getSubject()));
        }, rex_extension::EARLY, [
            'find' => $find,
            'type' => $type,
            'name' => $name,
            'header' => $header,
            'inline' => $inline,
            'minify' => $minify,
            'buster' => $buster
        ]);

        return '';
    }
}

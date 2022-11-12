<?php

/**
 * Asset handler
 * @author Daniel Weitenauer
 */
class theme_assets
{
    use \rex_instance_pool_trait {
        getInstance as getInstanceTrait;
    }
    use theme_assets_trait;

    private static string $active_instance_id;

    private string $id;

    private string $action = '';

    private string $cache_buster = '';

    private array $css = [];

    private array $css_inline = [];

    private array $js = [];

    private array $js_inline = [];

    private array $html = [];

    private array $attributes = [];

    public static function getInstance(string $key = 'default'): theme_assets
    {
        if ($key) {
            self::$active_instance_id = $key;
        } elseif (!isset(self::$active_instance_id)) {
            throw new rex_exception('ERROR: No instance set in '.static::class);
        }

        return static::getInstanceTrait(self::$active_instance_id, static function () {
            return new static(self::$active_instance_id);
        });
    }

    protected function __construct(string $id)
    {
        $this->id = $id;
    }

    public function setAction(string $action): theme_assets
    {
        $this->action = $action;

        return $this;
    }

    public function setCacheBuster(string $cache_buster): theme_assets
    {
        $this->cache_buster = $cache_buster;

        return $this;
    }

    public function setCss(string $key, string $data, string $media = 'all', array $attributes = []): theme_assets
    {
        $attributes['media'] = $media;

        $this->css[$key] = [
            'data' => $data,
            'attributes' => $attributes,
        ];

        return $this;
    }

    public function setCssInline(string $key, string $data, string $media = 'all'): theme_assets
    {
        $attributes['media'] = $media;

        $this->css_inline[$key] = [
            'data' => $data,
            'attributes' => $attributes,
        ];

        return $this;
    }

    public function setJs(string $key, string $data, bool $header = false, array $attributes = []): theme_assets
    {
        $this->js[$header ? 'header' : 'footer'][$key] = [
            'data' => $data,
            'attributes' => $attributes,
        ];

        return $this;
    }

    public function setJsInline(string $key, string $data, bool $header = false, array $attributes = []): theme_assets
    {
        $this->js_inline[$header ? 'header' : 'footer'][$key] = [
            'data' => $data,
            'attributes' => $attributes,
        ];

        return $this;
    }

    public function setHtml(string $key, string $data, bool $header = false): theme_assets
    {
        $this->html[$header ? 'header' : 'footer'][$key] = $data;

        return $this;
    }

    public function unsetCss(string $key): theme_assets
    {
        unset($this->css[$key]);

        return $this;
    }

    public function unsetCssInline(string $key): theme_assets
    {
        unset($this->css_inline[$key]);

        return $this;
    }

    public function unsetJs(string $key, bool $header = false): theme_assets
    {
        unset($this->js[$header ? 'header' : 'footer'][$key]);

        return $this;
    }

    public function unsetJsInline(string $key, bool $header = false): theme_assets
    {
        unset($this->js_inline[$header ? 'header' : 'footer'][$key]);

        return $this;
    }

    public function unsetHtml(string $key, bool $header = false): theme_assets
    {
        unset($this->js_inline[$header ? 'header' : 'footer'][$key]);

        return $this;
    }

    public function getCss(): string
    {
        if (empty($this->css)) {
            return '';
        }

        $return = rex_extension::registerPoint(new rex_extension_point('THEME_ASSETS_CSS', '', [
            'id' => $this->id,
            'action' => $this->action,
            'data' => $this->css,
        ]));

        if (!$return) {
            foreach ($this->css as $css_key => $css) {
                $return .= $this->getLinkTag($this->id.'--'.$css_key, $css['data'], $css['attributes'], $this->cache_buster);
            }
        }

        return $return;
    }

    public function getCssInline(): string
    {
        if (empty($this->css_inline)) {
            return '';
        }

        $return = rex_extension::registerPoint(new rex_extension_point('THEME_ASSETS_CSS_INLINE', '', [
            'id' => $this->id,
            'action' => $this->action,
            'data' => $this->css_inline,
        ]));

        $css_sets = [];
        foreach ($this->css_inline as $css_key => $css) {
            if (is_string($css['data'])) {
                $css_sets[$css['attributes']['media']] .= ($this->isAdmin() ? '/* '.$css_key.' */ ' : '').$css['data'].PHP_EOL;
            }
        }

        foreach ($css_sets as $css_key => $css_set) {
            $return .= '<style media='.$this->id.'--'.$css_key.'>'.PHP_EOL.$css_set.PHP_EOL.'</style>'.PHP_EOL;
        }

        return $return;
    }

    public function getJs(bool $header = false): string
    {
        $data = $this->js[$header ? 'header' : 'footer'] ?? [];

        if (empty($data)) {
            return '';
        }

        $return = rex_extension::registerPoint(new rex_extension_point('THEME_ASSETS_JS', '', [
            'id' => $this->id,
            'action' => $this->action,
            'data' => $data,
            'cache_buster' => $this->cache_buster,
            'header' => $header,
        ]));

        if (!$return) {
            foreach ($data as $file_key => $file) {
                $return .= $this->getScriptTag($this->id.'--'.$file_key, $file['data'], $file['attributes'], $this->cache_buster);
            }
        }

        return $return;
    }

    public function getJsInline(bool $header = false): string
    {
        $data = $this->js_inline[$header ? 'header' : 'footer'] ?? [];

        if (empty($data)) {
            return '';
        }

        $return = rex_extension::registerPoint(new rex_extension_point('THEME_ASSETS_JS_INLINE', '', [
            'id' => $this->id,
            'action' => $this->action,
            'data' => $data,
            'cache_buster' => $this->cache_buster,
            'header' => $header,
        ]));

        if (!$return) {
            foreach ($data as $js_key => $js) {
                $attributes = $js['attributes'];
                if ($this->isAdmin()) {
                    $attributes['class'] = (isset($attributes['class']) ? $attributes['class'].' ' : '').'script--'.$js_key;
                }

                $return .= '<script'.rex_string::buildAttributes($attributes).'>/*<![CDATA[*/'.PHP_EOL.$js['data'].'/*]]>*/</script>'.PHP_EOL;
            }
        }

        return $return;
    }

    public function getHtml(bool $header = false): string
    {
        $data = $this->html[$header ? 'header' : 'footer'] ?? [];

        if (empty($data)) {
            return '';
        }

        $return = rex_extension::registerPoint(new rex_extension_point('THEME_ASSETS_HTML', '', [
            'id' => $this->id,
            'action' => $this->action,
            'data' => $data,
        ]));

        foreach ($data as $html_key => $html) {
            if (is_string($html)) {
                $return .= ($this->isAdmin() ? '<!-- '.$this->id.'--'.$html_key.' -->'.PHP_EOL : '').$html.PHP_EOL;
            }
        }

        return $return;
    }
}

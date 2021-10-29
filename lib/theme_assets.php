<?php
/**
 * @author Daniel Weitenauer
 */

class theme_assets
{
    use rex_instance_pool_trait {
        getInstance as getInstanceTrait;
    }

    /**
     * @var string
     */
    protected static $active = '';
    /**
     * @var string
     */
    protected $action = '';
    /**
     * @var mixed
     */
    protected $cache_buster = false;
    /**
     * @var array
     */
    protected $data = [];

    public static function getInstance(string $key = 'default'): ?static
    {
        if ($key) {
            static::$active = $key;
        } elseif (!isset(static::$active)) {
            throw new rex_exception('ERROR: No instance set in '.static::class);
        }

        return static::getInstanceTrait(static::$active, function () {
            return new static();
        });
    }

    protected function __construct() {}

    public function setAction(string $action = 'minify'): theme_assets
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @param mixed $cache_buster
     */
    public function setCacheBuster($cache_buster = true): theme_assets
    {
        $this->cache_buster = $cache_buster;

        return $this;
    }

    public function setCss(string $key, string $data, string $media = 'all'): theme_assets
    {
        $this->data['css'][$key] = [
            'data' => $data,
            'media' => $media,
        ];

        return $this;
    }

    public function setCssInline(string $key, string $data, string $media = 'all'): theme_assets
    {
        $this->data['css_inline'][$key] = [
            'data' => $data,
            'media' => $media,
        ];

        return $this;
    }

    public function setJs(string $key, string $data, bool $header = false, array $attributes = []): theme_assets
    {
        $this->data['js'][$header ? 'header' : 'footer'][$key] = [
            'script' => $data,
            'attributes' => $attributes,
        ];

        return $this;
    }

    public function setJsInline(string $key, string $data, bool $header = false): theme_assets
    {
        $this->data['js_inline'][$header ? 'header' : 'footer'][$key] = $data;

        return $this;
    }

    public function setHtml(string $key, string $data, bool $header = false): theme_assets
    {
        $this->data['html'][$header ? 'header' : 'footer'][$key] = $data;

        return $this;
    }

    public function getCss(string $name = ''): string
    {
        $return = '';

        if (!rex_addon::get('minify')->isAvailable()) {
            $this->action = '';
        }

        if (!empty($this->data['css'])) {
            switch ($this->action) {
                case 'minify':
                case 'minify_css':
                    $minify = new minify();
                    $minify_sets = [];

                    // Build sets
                    foreach ($this->data['css'] as $css_key => $css) {
                        if (strpos($css['data'], 'http') === 0 || strpos($css['data'], '//') === 0) {
                            $return .= $this->getStyleTag($css_key, $css['data'], $css['media']);
                        } else {
                            $minify_sets[$css['media']][] = $css['data'];
                            $minify->addFile($css['data'], $name.'--'.$css['media']);
                        }
                    }

                    // Minify sets
                    foreach ($minify_sets as $media => $set) {
                        $return .= $this->getStyleTag($name.'--'.$media.'--minified', $minify->minify('css', $name.'--'.$media, 'file'), $media);
                    }
                    break;

                default:
                    foreach ($this->data['css'] as $css_key => $css) {
                        $return .= $this->getStyleTag($name.'--'.$css_key, $css['data'], $css['media']);
                    }
            }
        }

        return $return;
    }

    public function getCssInline(): string
    {
        $return = '';
        $css_sets = [];
        $user = $this->getUser();

        foreach ($this->data['css_inline'] as $css_key => $css) {
            if (is_string($css['data'])) {
                $css_sets[$css['media']] .= ($user && $user->isAdmin() ? '/* '.$css_key.' */ ' : '').$css['data'].PHP_EOL;
            }
        }
        foreach ($css_sets as $css_key => $css_set) {
            $return .= '<style media='.$css_key.'>'.PHP_EOL.$css_set.PHP_EOL.'</style>'.PHP_EOL;
        }

        return $return;
    }

    public function getJs(bool $header = false, string $name = ''): string
    {
        $return = '';
        $location = $header ? 'header' : 'footer';
        $files = $this->data['js'][$location] ?? [];

        if (!rex_addon::get('minify')->isAvailable()) {
            $this->action = '';
        }

        if (!empty($files)) {
            switch ($this->action) {
                case 'minify':
                case 'minify_js':
                    $minify = new minify();

                    foreach ($files as $file) {
                        $minify->addFile($file['script'], $name.'--'.$location);
                    }
                    $return .= $this->getScriptTag($name.'--'.$location.'--minified', $minify->minify('js', $name.'--'.$location, 'file'), []);
                    break;

                default:
                    foreach ($files as $file_key => $file) {
                        $return .= $this->getScriptTag($name.'--'.$file_key, $file['script'], $file['attributes']);
                    }
            }
        }

        return $return;

    }

    public function getJsInline(bool $header = false): string
    {
        $return = '';
        $location = $header ? 'header' : 'footer';
        $user = $this->getUser();

        if (isset($this->data['js_inline'][$location])) {
            foreach ($this->data['js_inline'][$location] as $key => $js) {
                if (is_string($js)) {
                    if ($user && $user->isAdmin()) {
                        $return .= '/* '.$key.' */'.PHP_EOL;
                    }
                    $return .= $js.PHP_EOL;
                }
            }
        }

        if ($return) {
            $return = '<script>/*<![CDATA[*/'.PHP_EOL.$return.'/*]]>*/</script>'.PHP_EOL;
        }

        return $return;

    }

    public function getHtml(bool $header = false): string
    {
        $return = '';
        $location = $header ? 'header' : 'footer';
        $user = $this->getUser();

        if (isset($this->data['html'][$location])) {
            foreach ($this->data['html'][$location] as $key => $html) {
                if (is_string($html)) {
                    $return .= ($user && $user->isAdmin() ? '<!-- '.$key.' -->'.PHP_EOL : '').$html.PHP_EOL;
                }
            }
        }

        return $return;
    }

    private function getScriptTag(string $key, string $script, array $attributes): string
    {
        $user = $this->getUser();

        return '<script '.implode(' ', $attributes).($user && $user->isAdmin() ? ' class="script--'.$key.'"' : '').' src="'.$this->stripDots($script).$this->getCacheBuster($script).'"></script>'.PHP_EOL;
    }

    private function getStyleTag(string $key, string $css, string $media): string
    {
        $user = $this->getUser();

        return '<link'.($user && $user->isAdmin() ? ' class="style--'.$key.'"' : '').' rel="stylesheet" type="text/css" href="'.$this->stripDots($css).$this->getCacheBuster($css).'" media="'.$media.'" />'.PHP_EOL;
    }

    private function stripDots(string $url): string
    {
        return ltrim($url, '.');
    }

    private function getCacheBuster(string $filename): string
    {
        if (in_array($this->cache_buster, ["true", "1"], true)) {
            $this->cache_buster = true;
        }

        if ($this->cache_buster === true) {
            $buster = 'buster='.time();
        } elseif ($this->cache_buster === 'timestamp') {
            $path = rex_path::base(ltrim($filename, '/'));
            $buster = 'timestamp='.filemtime($path);
        } else {
            $buster = 'version='. $this->cache_buster;
        }

        return $this->cache_buster ? (strpos($filename, '?') !== false ? '&' : '?').$buster : '';
    }

    private function getUser(): ?rex_user
    {
        if (!rex::isBackend()) {
            return rex_backend_login::createUser();
        }

        return rex::getUser();
    }
}

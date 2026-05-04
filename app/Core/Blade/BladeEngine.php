<?php

namespace App\Core;

class BladeEngine
{
    protected string $viewPath;
    protected string $cachePath;

    public function __construct()
    {
        $this->viewPath = rtrim(base_path("view/"), '/') . '/';
        $this->cachePath = base_path("cache/");

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    /* ================= RENDER ================= */
    public function render($file, $data = [])
    {
        $filePath = $this->viewPath . $file . ".blade.php";

        if (!file_exists($filePath)) {
            die("View not found: $file");
        }

        $cacheFile = $this->cachePath . md5($filePath) . ".php";

        // compile if not exists or updated
        if (!file_exists($cacheFile) || filemtime($filePath) > filemtime($cacheFile)) {
            $compiled = $this->compile(file_get_contents($filePath));
            file_put_contents($cacheFile, $compiled);
        }

        extract($this->sanitize($data));

        ob_start();
        include $cacheFile;
        return ob_get_clean();
    }

    /* ================= SANITIZE ================= */
    private function sanitize($data)
    {
        $safe = [];

        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $safe[$key] = htmlspecialchars($value, ENT_QUOTES, "UTF-8");
            } elseif (is_array($value)) {
                $safe[$key] = $this->sanitize($value);
            } else {
                $safe[$key] = $value;
            }
        }

        return $safe;
    }

    /* ================= COMPILER ================= */
    protected function compile($template)
    {
        $sections = [];
        $layout = null;

        /* @extends */
        $template = preg_replace_callback(
            '/@extends\([\'"](.+?)[\'"]\)/',
            function ($m) use (&$layout) {
                $layout = $m[1];
                return '';
            },
            $template
        );

        /* @section */
        $template = preg_replace_callback(
            '/@section\([\'"](.+?)[\'"]\)(.*?)@endsection/s',
            function ($m) use (&$sections) {
                $sections[$m[1]] = $m[2];
                return '';
            },
            $template
        );

        /* @include */
        $template = preg_replace_callback(
            '/@include\([\'"](.+?)[\'"]\)/',
            function ($m) {
                $file = $this->viewPath . $m[1] . ".blade.php";

                if (!file_exists($file)) {
                    return "<!-- include not found -->";
                }

                return $this->compile(file_get_contents($file));
            },
            $template
        );

        /* @yield replace for layout */
        if ($layout) {
            $layoutFile = $this->viewPath . $layout . ".blade.php";

            if (file_exists($layoutFile)) {
                $layoutCompiled = $this->compile(file_get_contents($layoutFile));

                foreach ($sections as $key => $value) {
                    $layoutCompiled = str_replace("@yield('$key')", $value, $layoutCompiled);
                }

                $template = $layoutCompiled;
            }
        }

        /* ================= OUTPUT ================= */

        // {{ $var }} (SAFE)
        $template = preg_replace_callback(
            '/\{\{\s*(.+?)\s*\}\}/',
            function ($m) {
                return '<?php echo htmlspecialchars(' . $m[1] . ', ENT_QUOTES, "UTF-8"); ?>';
            },
            $template
        );

        // {!! $var !!} (RAW)
        $template = preg_replace(
            '/\{!!\s*(.+?)\s*!!\}/',
            '<?php echo $1; ?>',
            $template
        );

        /* IF */
        $template = preg_replace('/@if\s*\((.+?)\)/', '<?php if($1): ?>', $template);
        $template = preg_replace('/@elseif\s*\((.+?)\)/', '<?php elseif($1): ?>', $template);
        $template = str_replace('@else', '<?php else: ?>', $template);
        $template = str_replace('@endif', '<?php endif; ?>', $template);

        /* FOREACH */
        $template = preg_replace('/@foreach\s*\((.+?)\)/', '<?php foreach($1): ?>', $template);
        $template = str_replace('@endforeach', '<?php endforeach; ?>', $template);

        return $template;
    }
}
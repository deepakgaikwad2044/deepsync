<?php

namespace App\Core;

class Blade
{
    protected string $viewPath;
    protected string $cachePath;

    public function __construct()
    {
        $this->viewPath = rtrim(base_path("view/"), '/') . '/';
        $this->cachePath = base_path("/bootstrap/cache/");

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    /* ================= RENDER ================= */
    public function render($file, $data = [])
    {
        $filePath = $this->viewPath . str_replace('.', '/', $file) . ".blade.php";

        if (!file_exists($filePath)) {
            die("View not found: $file");
        }

        $cacheFile = $this->cachePath . md5($filePath) . ".php";

        if (!file_exists($cacheFile) || filemtime($filePath) > filemtime($cacheFile)) {
            $compiled = $this->compile(file_get_contents($filePath));
            file_put_contents($cacheFile, $compiled);
        }

        extract($data);

        ob_start();
        include $cacheFile;
        return ob_get_clean();
    }

    /* ================= COMPILER ================= */
    protected function compile($template)
    {
        $sections = [];
        $layout = null;
        
        
        /* ================= VERBATIM ================= */
$verbatimBlocks = [];

$template = preg_replace_callback(
    '/@verbatim(.*?)@endverbatim/s',
    function ($m) use (&$verbatimBlocks) {
        $key = '__VERBATIM__' . count($verbatimBlocks) . '__';
        $verbatimBlocks[$key] = $m[1]; // store raw content
        return $key; // replace with placeholder
    },
    $template
);


        /* ========= @extends ========= */
        $template = preg_replace_callback(
            '/@extends\([\'"](.+?)[\'"]\)/',
            function ($m) use (&$layout) {
                $layout = str_replace('.', '/', $m[1]);
                return '';
            },
            $template
        );

        /* ========= @section ========= */
        $template = preg_replace_callback(
            '/@section\([\'"](.+?)[\'"]\)(.*?)@endsection/s',
            function ($m) use (&$sections) {
                $sections[$m[1]] = $m[2];
                return '';
            },
            $template
        );

        /* ========= Layout Merge ========= */
        if ($layout) {
            $layoutFile = $this->viewPath . $layout . ".blade.php";

            if (file_exists($layoutFile)) {
                $layoutContent = file_get_contents($layoutFile);

                foreach ($sections as $key => $value) {
                    $pattern = '/@yield\([\'"]' . preg_quote($key, '/') . '[\'"]\)/';
                    $layoutContent = preg_replace($pattern, $value, $layoutContent);
                }

                $template = $layoutContent;
            }
        }

        /* ========= @include (SAFE) ========= */
        $template = preg_replace_callback(
            '/@include\([\'"](.+?)[\'"]\)/',
            function ($m) {
                $file = $this->viewPath . str_replace('.', '/', $m[1]) . ".blade.php";

                if (!file_exists($file)) {
                    return "<!-- include not found: {$m[1]} -->";
                }

                // inline compile → no render() → no recursion
                return $this->compile(file_get_contents($file));
            },
            $template
        );

        /* ================= COMMENTS ================= */
        $template = preg_replace('/\{\{\-\-(.*?)\-\-\}\}/s', '', $template);

        /* ================= @php ================= */
        $template = preg_replace_callback(
            '/@php(.*?)@endphp/s',
            fn($m) => '<?php ' . $m[1] . ' ?>',
            $template
        );

        /* ================= RAW ================= */
        $template = preg_replace(
            '/\{!!\s*(.*?)\s*!!\}/s',
            '<?php echo $1; ?>',
            $template
        );

        /* ================= SAFE ================= */
        $template = preg_replace(
            '/\{\{\s*(.*?)\s*\}\}/',
            '<?php echo htmlspecialchars($1, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8"); ?>',
            $template
        );

        /* ================= CONDITIONS (FIXED) ================= */
        $template = preg_replace_callback(
            '/@if\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/',
            function ($m) {
                $cond = trim($m[1]) ?: 'false';
                return "<?php if($cond): ?>";
            },
            $template
        );

        $template = preg_replace_callback(
            '/@elseif\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/',
            function ($m) {
                $cond = trim($m[1]) ?: 'false';
                return "<?php elseif($cond): ?>";
            },
            $template
        );

        $template = preg_replace('/@else\b/', '<?php else: ?>', $template);
        $template = preg_replace('/@endif\b/', '<?php endif; ?>', $template);

        /* ================= LOOPS ================= */
        $template = preg_replace(
            '/@foreach\s*\((.*?)\)/',
            '<?php foreach($1): ?>',
            $template
        );

        $template = preg_replace('/@endforeach/', '<?php endforeach; ?>', $template);



/* ================= RESTORE VERBATIM ================= */
if (!empty($verbatimBlocks)) {
    $template = str_replace(
        array_keys($verbatimBlocks),
        array_values($verbatimBlocks),
        $template
    );
}
        return $template;
    }
}
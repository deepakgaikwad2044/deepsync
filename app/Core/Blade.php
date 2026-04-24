<?php

namespace App\Core;

class Blade
{
    protected string $viewPath;

    public function __construct()
    {
        $this->viewPath = rtrim(base_path("view/"), '/') . '/';
    }

    /* ================= RENDER ================= */
    public function render($file, $data = [])
    {
        $viewFile = $this->viewPath . $file . ".blade.php";

        if (!file_exists($viewFile)) {
            throw new \Exception("View not found: " . $file);
        }

        $template = file_get_contents($viewFile);
        $compiled = $this->compile($template);

        extract($data);

        ob_start();
        eval('?>' . $compiled);
        return ob_get_clean();
    }

    /* ================= COMPILER ================= */
    protected function compile($template)
    {
        $sections = [];
        $parent = null;

        /* ---------- @extends ---------- */
        $template = preg_replace_callback(
            '/@extends\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
            function ($m) use (&$parent) {
                $parent = $m[1];
                return '';
            },
            $template
        );

        /* ---------- @section ---------- */
        $template = preg_replace_callback(
            '/@section\s*\(\s*[\'"](.+?)[\'"]\s*\)(.*?)@endsection/s',
            function ($m) use (&$sections) {
                $sections[$m[1]] = $m[2];
                return '';
            },
            $template
        );

        /* ---------- @include ---------- */
        $template = preg_replace_callback(
            '/@include\s*\(\s*[\'"](.+?)[\'"]\s*\)/',
            function ($m) {
                $file = rtrim(base_path("view/"), '/') . '/' . $m[1] . ".blade.php";

                if (!file_exists($file)) {
                    return "<!-- include not found: {$m[1]} -->";
                }

                return file_get_contents($file);
            },
            $template
        );

      /* {{ }} SAFE OUTPUT */
        $template = preg_replace_callback(
            '/\{\{\s*(.+?)\s*\}\}/',
            function ($m) {
                return '<?php echo htmlspecialchars(' . $m[1] . ', ENT_QUOTES, "UTF-8"); ?>';
            },
            $template
        );

        /* {!! !!} RAW OUTPUT */
        $template = preg_replace_callback(
            '/\{!!\s*(.+?)\s*!!\}/',
            function ($m) {
                return '<?php echo ' . $m[1] . '; ?>';
            },
            $template
        );

        /* ---------- IF / ELSE ---------- */
        $template = preg_replace('/@if\s*\((.+?)\)/', '<?php if($1): ?>', $template);
        $template = preg_replace('/@elseif\s*\((.+?)\)/', '<?php elseif($1): ?>', $template);
        $template = str_replace('@else', '<?php else: ?>', $template);
        $template = str_replace('@endif', '<?php endif; ?>', $template);

        /* ---------- FOREACH ---------- */
        $template = preg_replace('/@foreach\s*\((.+?)\)/', '<?php foreach($1): ?>', $template);
        $template = str_replace('@endforeach', '<?php endforeach; ?>', $template);

        /* ---------- HANDLE LAYOUT ---------- */
        if ($parent) {

            $parentFile = $this->viewPath . $parent . ".blade.php";

            if (!file_exists($parentFile)) {
                return $template;
            }

            $parentTemplate = file_get_contents($parentFile);

            /* inject sections into layout */
            foreach ($sections as $key => $value) {
                $parentTemplate = str_replace("@yield('$key')", $value, $parentTemplate);
                $parentTemplate = str_replace("@yield(\"$key\")", $value, $parentTemplate);
            }

            /* compile final merged layout */
            return $this->compile($parentTemplate);
        }

        return $template;
    }
}


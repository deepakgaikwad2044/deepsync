<?php

namespace App\Core;

class Pranchi
{
    protected string $viewPath;
    protected string $cachePath;

    protected array $sections = [];
    protected ?string $layout = null;

    public function __construct()
    {
        $this->viewPath = rtrim(base_path("views/"), "/") . "/";
        $this->cachePath = base_path("/bootstrap/cache/");

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    /* ================= RENDER ================= */
    public function render($view, $data = [])
    {
        $filePath = $this->viewPath . str_replace(".", "/", $view) . ".pra.php";

        if (!file_exists($filePath)) {
            throw new \Exception("View not found: $view");
        }

        $cacheFile =
            $this->cachePath . sha1($filePath . filemtime($filePath)) . ".php";

        if (!file_exists($cacheFile)) {
            $compiled = $this->compile(file_get_contents($filePath));

            file_put_contents($cacheFile, $compiled);
        }

        /* ========= GLOBAL SHARED DATA ========= */

        $shared = [

            /* ========= VALIDATION ========= */

            "errors" => function_exists("errors") ? errors() : [],

            "old" => function_exists("old") ? old() : [],

            /* ========= ALERTS ========= */

            "success" => function_exists("success") ? success() : null,

            "error" => function_exists("error") ? error() : null,

            /* ========= SESSION ========= */

            "verified" => $_SESSION["verified"] ?? false,
        ];

        unset($_SESSION["errors"], $_SESSION["old"]);

        // merge controller data with shared data
        $data = array_merge($shared, $data);

        // SAFE extract
        extract($data, EXTR_SKIP);

        ob_start();

        try {
            include $cacheFile;
        } catch (\Throwable $e) {
            ob_end_clean();

            throw $e;
        }

        return ob_get_clean();
    }

    /* ================= COMPILER ================= */
    protected function compile($template)
    {
        $this->sections = [];
        $this->layout = null;

        /* ========= BLADE COMMENTS ========= */
        $template = preg_replace("/\{\{\s*--.*?--\s*\}\}/s", "", $template);

        /* ========= @extends ========= */
        $template = preg_replace_callback(
            '/@extends\([\'"](.+?)["\']\)/',
            function ($m) {
                $this->layout = str_replace(".", "/", $m[1]);
                return "";
            },
            $template
        );

        /* ========= @section ========= */
        $template = preg_replace_callback(
            '/@section\([\'"](.+?)["\']\)(.*?)@endsection/s',
            function ($m) {
                $this->sections[$m[1]] = $m[2];
                return "";
            },
            $template
        );

        /* ========= LAYOUT MERGE ========= */
        if ($this->layout) {

            $layoutFile = $this->viewPath . $this->layout . ".pra.php";

            if (file_exists($layoutFile)) {

                $layoutContent = file_get_contents($layoutFile);

                foreach ($this->sections as $key => $value) {

                    $layoutContent = preg_replace(
                        '/@yield\([\'"]' . $key . '[\'"]\)/',
                        $value,
                        $layoutContent
                    );
                }

                // remove unused yields
                $layoutContent = preg_replace("/@yield\([^)]+\)/", "", $layoutContent);

                $template = $layoutContent;
            }
        }

        /* ========= @include ========= */
        $template = preg_replace_callback(
            '/@include\([\'"](.+?)["\']\)/',
            function ($m) {

                $file = $this->viewPath . str_replace(".", "/", $m[1]) . ".pra.php";

                if (!file_exists($file)) {
                    return "<!-- include not found -->";
                }

                return $this->compile(file_get_contents($file));
            },
            $template
        );

        /* ========= @php BLOCK ========= */
        $template = preg_replace_callback(
            "/@php(.*?)@endphp/s",
            function ($m) {
                return "<?php " . trim($m[1]) . " ?>";
            },
            $template
        );

        /* ========= SAFE OUTPUT ========= */
        $template = preg_replace(
            "/\{\{\s*(.*?)\s*\}\}/",
            '<?php echo e($1); ?>',
            $template
        );

        /* ========= RAW OUTPUT ========= */
        $template = preg_replace(
            "/\{!!\s*(.*?)\s*!!\}/s",
            '<?php echo $1; ?>',
            $template
        );

        /* ========= CONDITIONS ========= */
        $template = preg_replace_callback(
            "/@if\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/",
            fn($m) => "<?php if(" . $m[1] . "): ?>",
            $template
        );

        $template = preg_replace_callback(
            "/@elseif\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/",
            fn($m) => "<?php elseif(" . $m[1] . "): ?>",
            $template
        );

        $template = preg_replace("/@else\b/", "<?php else: ?>", $template);
        $template = preg_replace("/@endif\b/", "<?php endif; ?>", $template);

        /* ========= @error DIRECTIVE ========= */

        $template = preg_replace_callback(
            '/@error\([\'"](.+?)[\'"]\)/',
            function ($m) {

                $field = $m[1];

                return "<?php if(!empty(\$errors['$field'])): \$message = \$errors['$field']; ?>";
            },
            $template
        );

        $template = preg_replace("/@enderror/", "<?php endif; ?>", $template);

        /* ========= LOOPS ========= */

        $template = preg_replace(
            "/@foreach\s*\((.*?)\)/",
            '<?php foreach($1): ?>',
            $template
        );

        $template = preg_replace(
            "/@endforeach/",
            "<?php endforeach; ?>",
            $template
        );

        /* ========= FLASH SUCCESS ========= */

        $template = preg_replace_callback(
            "/@flashSuccess\b/",
            function () {
                return <<<PHP
<?php if(\$message = get_flash('success')): ?>
<div class="success-alert">
    <?php echo e(\$message); ?>
</div>
<?php endif; ?>
PHP;
            },
            $template
        );

        /* ========= FLASH ERROR ========= */

        $template = preg_replace_callback(
            "/@flashError\b/",
            function () {
                return <<<PHP
<?php if(\$message = get_flash('error')): ?>
<div class="error-alert">
    <?php echo e(\$message); ?>
</div>
<?php endif; ?>
PHP;
            },
            $template
        );

        /* ========= TOSTER ========= */

        $template = preg_replace_callback(
            "/@toster\b/",
            function () {
                return <<<PHP
<?php

if(isset(\$_SESSION['flash_success'])) {

    \$msg = json_encode(\$_SESSION['flash_success']);

    echo "<script>
        toastr.success(\$msg, 'Success');
    </script>";

    unset(\$_SESSION['flash_success']);
}

if(isset(\$_SESSION['flash_error'])) {

    \$msg = json_encode(\$_SESSION['flash_error']);

    echo "<script>
        toastr.error(\$msg, 'Error');
    </script>";

    unset(\$_SESSION['flash_error']);
}

?>
PHP;
            },
            $template
        );

        /* ========= CSRF ========= */

        $template = preg_replace(
            "/@csrf\b/",
            "<?= csrf_field() ?>",
            $template
        );

        return $template;
    }
}

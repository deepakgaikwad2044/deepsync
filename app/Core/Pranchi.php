<?php

declare(strict_types=1);

namespace App\Core;

class Pranchi
{
    protected string $viewPath;
    protected string $cachePath;

    protected array $sections = [];
    protected ?string $layout = null;
protected ?\App\Core\Components\ComponentCompiler $componentCompiler = null;

    public function __construct()
    {
        $this->viewPath  = rtrim(base_path("views/"), "/") . "/";
        $this->cachePath = rtrim(base_path("bootstrap/cache/"), "/") . "/";

        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    /* =========================================================
     | RENDER
     * ========================================================= */

    public function render(string $view, array $data = []): string
    {
        $filePath = $this->viewPath .
            str_replace(".", "/", $view) .
            ".pra.php";

        if (!file_exists($filePath)) {
            throw new \Exception("View not found: {$view}");
        }

        /* ========= CACHE FILE ========= */

        $cacheFile = $this->cachePath .
            md5($filePath) .
            ".php";

        /* ========= AUTO RECOMPILE ========= */

        if (
            !file_exists($cacheFile) ||
            filemtime($cacheFile) < filemtime($filePath)
        ) {

   $content = file_get_contents($filePath);

// 👇 FIRST compile components BEFORE anything
if ($this->componentCompiler) {
    $content = $this->componentCompiler->compile($content);
}

// then blade compile
$compiled = $this->compile($content);

            $compiled =
                "<?php /* PRANCHI compiled template */ ?>\n" .
                $compiled;

            file_put_contents($cacheFile, $compiled);
        }

        /* ========= SHARED DATA ========= */

        $shared = [

            /* ========= VALIDATION ========= */

            "errors" => function_exists("errors")
                ? errors()
                : [],

            "old" => function_exists("old")
                ? old()
                : [],

            /* ========= FLASH ========= */

            "success" => function_exists("success")
                ? success()
                : null,

            "error" => function_exists("error")
                ? error()
                : null,

            /* ========= SESSION ========= */

            "verified" => $_SESSION["verified"] ?? false,
        ];

        unset($_SESSION["errors"], $_SESSION["old"]);

        $data = array_merge($shared, $data);

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

    /* =========================================================
     | COMPILER
     * ========================================================= */

    protected function compile(string $template): string
    {
        $this->sections = [];
        $this->layout   = null;

        /* ========= COMMENTS ========= */

        $template = preg_replace(
            "/\{\{\s*--.*?--\s*\}\}/s",
            "",
            $template
        );

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

        /* ========= LAYOUT ========= */

        if ($this->layout) {

            $layoutFile = $this->viewPath .
                $this->layout .
                ".pra.php";

            if (file_exists($layoutFile)) {

                $layoutContent = file_get_contents($layoutFile);

                foreach ($this->sections as $key => $value) {

                    $layoutContent = preg_replace(
                        '/@yield\([\'"]' . $key . '[\'"]\)/',
                        $value,
                        $layoutContent
                    );
                }

                $layoutContent = preg_replace(
                    "/@yield\([^)]+\)/",
                    "",
                    $layoutContent
                );

                $template = $layoutContent;
            }
        }

        /* ========= @include ========= */

        $template = preg_replace_callback(
            '/@include\([\'"](.+?)["\']\)/',
            function ($m) {

                $file = $this->viewPath .
                    str_replace(".", "/", $m[1]) .
                    ".pra.php";

                $real = realpath($file);

                if (
                    !$real ||
                    !str_starts_with(
                        $real,
                        realpath($this->viewPath)
                    )
                ) {
                    throw new \Exception(
                        "Invalid include path"
                    );
                }

                if (!file_exists($real)) {
                    return "<!-- include not found -->";
                }

                return $this->compile(
                    file_get_contents($real)
                );
            },
            $template
        );

        /* ========= @php ========= */

        $template = preg_replace_callback(
            "/@php(.*?)@endphp/s",
            function ($m) {

                return "<?php " .
                    trim($m[1]) .
                    " ?>";
            },
            $template
        );
        
        
        /* ========= @props ========= */

$template = preg_replace_callback(
    '/@props\((.*?)\)/s',
    function ($m) {
        return "<?php\n"
            . '$__defaults = ' . $m[1] . ";\n"
            . "foreach ((array) \$__defaults as \$__key => \$__default) {\n"
            . "    if (!isset(\$__props[\$__key])) {\n"
            . "        \$__props[\$__key] = \$__default;\n"
            . "    }\n"
            . "    \${\$__key} = \$__props[\$__key];\n"
            . "}\n"
            . "?>";
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

        $template = preg_replace(
            "/@else\b/",
            "<?php else: ?>",
            $template
        );

        $template = preg_replace(
            "/@endif\b/",
            "<?php endif; ?>",
            $template
        );

        /* ========= @error ========= */

        $template = preg_replace_callback(
            '/@error\([\'"](.+?)[\'"]\)/',
            function ($m) {

                $field = $m[1];

                return "<?php if(!empty(\$errors['{$field}'])): \$message = \$errors['{$field}']; ?>";
            },
            $template
        );

        $template = preg_replace(
            "/@enderror/",
            "<?php endif; ?>",
            $template
        );

        /* ========= LOOPS ========= */

        $template = preg_replace(
            "/@foreach\s*\((.*?)\)/",
            "<?php foreach($1): ?>",
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

    \$msg = json_encode(
        \$_SESSION['flash_success']
    );

    echo "<script>
        toastr.success(\$msg, 'Success');
    </script>";

    unset(\$_SESSION['flash_success']);
}

if(isset(\$_SESSION['flash_error'])) {

    \$msg = json_encode(
        \$_SESSION['flash_error']
    );

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
        
        
        if ($this->componentCompiler) {
    $template = $this->componentCompiler->compile($template);
}

        return $template;
    }
    
    
public function setComponentCompiler($compiler): void
{
    $this->componentCompiler = $compiler;
}


public function getComponentCompiler(): ?\App\Core\Components\ComponentCompiler
{
    return $this->componentCompiler;
}
}




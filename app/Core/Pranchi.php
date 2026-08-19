<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Components\ComponentCompiler;
use Throwable;

class Pranchi
{
    protected string $viewPath;
    protected string $cachePath;

    protected array $sections = [];
    protected ?string $layout = null;

    protected ?ComponentCompiler $componentCompiler = null;
  

    /*
    |--------------------------------------------------------------------------
    | Security settings
    |--------------------------------------------------------------------------
    */

    protected bool $allowRawOutput = true;
    protected bool $allowPhpBlocks = true;


    public function __construct()
    {
        $this->viewPath = rtrim(
            base_path("views"),
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR;

        $this->cachePath = rtrim(
            base_path("bootstrap/cache"),
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR;


        /*
        |--------------------------------------------------------------------------
        | Validate view directory
        |--------------------------------------------------------------------------
        */

        if (!is_dir($this->viewPath)) {
            throw new \RuntimeException(
                "PRANCHI view directory does not exist."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Create cache directory
        |--------------------------------------------------------------------------
        */

        if (!is_dir($this->cachePath)) {

            if (!mkdir($this->cachePath, 0775, true)) {
                throw new \RuntimeException(
                    "Unable to create PRANCHI cache directory."
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validate cache directory
        |--------------------------------------------------------------------------
        */

        if (!is_writable($this->cachePath)) {
            throw new \RuntimeException(
                "PRANCHI cache directory is not writable."
            );
        }
    }


    /* =========================================================
     | RENDER
     ========================================================= */

    public function render(
        string $view,
        array $data = []
    ): string {

        /*
        |--------------------------------------------------------------------------
        | Validate view name
        |--------------------------------------------------------------------------
        */

        $this->validateViewName($view);


        /*
        |--------------------------------------------------------------------------
        | Resolve view file
        |--------------------------------------------------------------------------
        */

        $filePath =
            $this->viewPath .
            str_replace(".", "/", $view) .
            ".pra.php";


        $realViewPath = realpath($filePath);
        $realBasePath = realpath($this->viewPath);


        if (
            $realViewPath === false ||
            $realBasePath === false ||
            !$this->isPathInside(
                $realViewPath,
                $realBasePath
            )
        ) {
            throw new \RuntimeException(
                "Invalid view path."
            );
        }


        if (!is_file($realViewPath)) {
            throw new \RuntimeException(
                "View not found: {$view}"
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Cache file
        |--------------------------------------------------------------------------
        */

        $cacheFile =
            $this->cachePath .
            hash(
                "sha256",
                $realViewPath
            ) .
            ".php";


        /*
        |--------------------------------------------------------------------------
        | Compile if necessary
        |--------------------------------------------------------------------------
        */

        if (
            !file_exists($cacheFile) ||
            filemtime($cacheFile) < filemtime($realViewPath)
        ) {

            $content =
                file_get_contents($realViewPath);


            if ($content === false) {
                throw new \RuntimeException(
                    "Unable to read view: {$view}"
                );
            }


            $compiled =
                $this->compile($content);


            /*
            |--------------------------------------------------------------------------
            | PHP cache header
            |--------------------------------------------------------------------------
            */

            $compiled =
                "<?php\n" .
                "/* PRANCHI compiled template */\n" .
                "/* DO NOT EDIT THIS FILE */\n" .
                "?>\n" .
                $compiled;


            /*
            |--------------------------------------------------------------------------
            | Atomic cache write
            |--------------------------------------------------------------------------
            */

            $temporaryFile =
                $cacheFile .
                "." .
                bin2hex(random_bytes(6)) .
                ".tmp";


       $result = file_put_contents(
    $temporaryFile,
    $compiled
);

if ($result === false) {
    throw new \RuntimeException(
        "Unable to write PRANCHI cache."
    );
}


            if (!rename(
                $temporaryFile,
                $cacheFile
            )) {

                @unlink($temporaryFile);

                throw new \RuntimeException(
                    "Unable to finalize PRANCHI cache."
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Shared data
        |--------------------------------------------------------------------------
        */

        $shared = [

            "errors" =>
                function_exists("errors")
                    ? errors()
                    : [],

            "old" =>
                function_exists("old")
                    ? old()
                    : [],

            "success" =>
                function_exists("success")
                    ? success()
                    : null,

            "error" =>
                function_exists("error")
                    ? error()
                    : null,

            "verified" =>
                $_SESSION["verified"] ?? false,
        ];


        /*
        |--------------------------------------------------------------------------
        | Clear consumed validation data
        |--------------------------------------------------------------------------
        */

        if (isset($_SESSION["errors"])) {
            unset($_SESSION["errors"]);
        }

        if (isset($_SESSION["old"])) {
            unset($_SESSION["old"]);
        }


        /*
        |--------------------------------------------------------------------------
        | Merge data
        |--------------------------------------------------------------------------
        */

        $data =
            array_merge(
                $shared,
                $data
            );


        /*
        |--------------------------------------------------------------------------
        | Extract safely
        |--------------------------------------------------------------------------
        */


$__componentRenderer =
    $this->componentCompiler?->getRenderer();

$data['__componentRenderer'] =
    $this->componentCompiler?->getRenderer();
    
        extract(
            $data,
            EXTR_SKIP
        );


        /*
        |--------------------------------------------------------------------------
        | Execute compiled view
        |--------------------------------------------------------------------------
        */

        ob_start();

        try {

            include $cacheFile;

        } catch (Throwable $e) {

            ob_end_clean();

            throw $e;
        }


        return ob_get_clean();
    }


    /* =========================================================
     | COMPILER
     ========================================================= */

    protected function compile(
        string $template
    ): string {

        $this->sections = [];
        $this->layout = null;


        /*
        |--------------------------------------------------------------------------
        | PRANCHI comments
        |
        | {{-- comment --}}
        |--------------------------------------------------------------------------
        */

        $template = preg_replace(
            "/\{\{\-\-(.*?)\-\-\}\}/s",
            "",
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @extends
        |--------------------------------------------------------------------------
        */

        $template = preg_replace_callback(
            '/@extends\([\'"](.+?)[\'"]\)/',
            function ($match) {

                $layout =
                    trim($match[1]);


                $this->validateViewName(
                    $layout
                );


                $this->layout =
                    str_replace(
                        ".",
                        "/",
                        $layout
                    );


                return "";
            },
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @section
        |--------------------------------------------------------------------------
        */

        $template = preg_replace_callback(
            '/@section\([\'"](.+?)[\'"]\)(.*?)@endsection/s',
            function ($match) {

                $name =
                    trim($match[1]);


                if ($name === "") {
                    throw new \RuntimeException(
                        "PRANCHI section name cannot be empty."
                    );
                }


                $this->sections[$name] =
                    $match[2];


                return "";
            },
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | Layout
        |--------------------------------------------------------------------------
        */

        if ($this->layout !== null) {

            $layoutFile =
                $this->viewPath .
                $this->layout .
                ".pra.php";


            $realLayout =
                realpath($layoutFile);


            $realBase =
                realpath($this->viewPath);


            if (
                $realLayout === false ||
                $realBase === false ||
                !$this->isPathInside(
                    $realLayout,
                    $realBase
                )
            ) {

                throw new \RuntimeException(
                    "Invalid layout path."
                );
            }


            if (!is_file($realLayout)) {

                throw new \RuntimeException(
                    "Layout not found: {$this->layout}"
                );
            }


            $layoutContent =
                file_get_contents($realLayout);


            if ($layoutContent === false) {
                throw new \RuntimeException(
                    "Unable to read layout."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Insert sections
            |--------------------------------------------------------------------------
            */

            foreach (
                $this->sections
                as $key => $value
            ) {

                $pattern =
                    '/@yield\([\'"]' .
                    preg_quote(
                        $key,
                        "/"
                    ) .
                    '[\'"]\)/';


                $layoutContent =
                    preg_replace(
                        $pattern,
                        $value,
                        $layoutContent
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Remove unused yields
            |--------------------------------------------------------------------------
            */

            $layoutContent =
                preg_replace(
                    "/@yield\([^)]+\)/",
                    "",
                    $layoutContent
                );


            $template =
                $layoutContent;
        }


        /*
        |--------------------------------------------------------------------------
        | @include
        |--------------------------------------------------------------------------
        */

        $template = preg_replace_callback(
            '/@include\([\'"](.+?)[\'"]\)/',
            function ($match) {

                $include =
                    trim($match[1]);


                $this->validateViewName(
                    $include
                );


                $file =
                    $this->viewPath .
                    str_replace(
                        ".",
                        "/",
                        $include
                    ) .
                    ".pra.php";


                $real =
                    realpath($file);


                $base =
                    realpath($this->viewPath);


                if (
                    $real === false ||
                    $base === false ||
                    !$this->isPathInside(
                        $real,
                        $base
                    )
                ) {

                    throw new \RuntimeException(
                        "Invalid include path."
                    );
                }


                if (!is_file($real)) {

                    throw new \RuntimeException(
                        "Included view not found: {$include}"
                    );
                }


                $includedContent =
                    file_get_contents($real);


                if ($includedContent === false) {
                    throw new \RuntimeException(
                        "Unable to read included view."
                    );
                }


                return $this->compile(
                    $includedContent
                );
            },
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @php
        |--------------------------------------------------------------------------
        */

        if ($this->allowPhpBlocks) {

            $template = preg_replace_callback(
                "/@php(.*?)@endphp/s",
                function ($match) {

                    return
                        "<?php " .
                        trim($match[1]) .
                        " ?>";
                },
                $template
            );
        }


        /*
        |--------------------------------------------------------------------------
        | @props
        |--------------------------------------------------------------------------
        */

        $template = preg_replace_callback(
            '/@props\((.*?)\)/s',
            function ($match) {

                return
                    "<?php\n" .

                    '$__defaults = ' .
                    $match[1] .
                    ";\n" .

                    "foreach ((array) \$__defaults as \$__key => \$__default) {\n" .

                    "    if (!isset(\$__props[\$__key])) {\n" .

                    "        \$__props[\$__key] = \$__default;\n" .

                    "    }\n" .

                    "    \${\$__key} = \$__props[\$__key];\n" .

                    "}\n" .

                    "?>";
            },
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | RAW OUTPUT
        |
        | {!! $html !!}
        |--------------------------------------------------------------------------
        */

        if ($this->allowRawOutput) {

            $template = preg_replace(
                "/\{!!\s*(.*?)\s*!!\}/s",
                '<?php echo $1; ?>',
                $template
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SAFE OUTPUT
        |
        | {{ $value }}
        |--------------------------------------------------------------------------
        */

        $template = preg_replace_callback(
            "/\{\{\s*(.*?)\s*\}\}/s",
            function ($match) {

                $expression =
                    trim($match[1]);


                /*
                |--------------------------------------------------------------------------
                | Ignore comment-like expression
                |--------------------------------------------------------------------------
                */

                if (
                    str_starts_with(
                        $expression,
                        "--"
                    )
                ) {
                    return "";
                }


                if ($expression === "") {
                    return "";
                }


                return
                    "<?php echo e(" .
                    $expression .
                    "); ?>";
            },
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @if
        |--------------------------------------------------------------------------
        */

        $template = preg_replace_callback(
            "/@if\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/",
            fn($match) =>
                "<?php if(" .
                $match[1] .
                "): ?>",
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @elseif
        |--------------------------------------------------------------------------
        */

        $template = preg_replace_callback(
            "/@elseif\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/",
            fn($match) =>
                "<?php elseif(" .
                $match[1] .
                "): ?>",
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @else
        |--------------------------------------------------------------------------
        */

        $template = preg_replace(
            "/@else\b/",
            "<?php else: ?>",
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @endif
        |--------------------------------------------------------------------------
        */

        $template = preg_replace(
            "/@endif\b/",
            "<?php endif; ?>",
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @error
        |--------------------------------------------------------------------------
        */

        $template = preg_replace_callback(
            '/@error\([\'"](.+?)[\'"]\)/',
            function ($match) {

                $field =
                    addslashes(
                        $match[1]
                    );


                return
                    "<?php " .
                    "if(!empty(\$errors['{$field}'])): " .
                    "\$message = \$errors['{$field}']; ?>";
            },
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @enderror
        |--------------------------------------------------------------------------
        */

        $template = preg_replace(
            "/@enderror/",
            "<?php endif; ?>",
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @foreach
        |--------------------------------------------------------------------------
        */

        $template = preg_replace(
            "/@foreach\s*\((.*?)\)/",
            "<?php foreach($1): ?>",
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @endforeach
        |--------------------------------------------------------------------------
        */

        $template = preg_replace(
            "/@endforeach/",
            "<?php endforeach; ?>",
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @flashSuccess
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | @flashError
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | @toster
        |--------------------------------------------------------------------------
        */

        $template = preg_replace_callback(
            "/@toster\b/",
            function () {

                return <<<PHP
<?php

if (isset(\$_SESSION['flash_success'])) {

    \$msg = json_encode(
        \$_SESSION['flash_success'],
        JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP
    );

    echo "<script>
        toastr.success(\$msg, 'Success');
    </script>";

    unset(
        \$_SESSION['flash_success']
    );
}


if (isset(\$_SESSION['flash_error'])) {

    \$msg = json_encode(
        \$_SESSION['flash_error'],
        JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP
    );

    echo "<script>
        toastr.error(\$msg, 'Error');
    </script>";

    unset(
        \$_SESSION['flash_error']
    );
}

?>
PHP;
            },
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | @csrf
        |--------------------------------------------------------------------------
        */

        $template = preg_replace(
            "/@csrf\b/",
            "<?= csrf_field() ?>",
            $template
        );


        /*
        |--------------------------------------------------------------------------
        | COMPONENT COMPILER
        |
        | Compile exactly once.
        |--------------------------------------------------------------------------
        */

        if ($this->componentCompiler !== null) {

            $template =
                $this->componentCompiler
                    ->compile($template);
        }


        return $template;
    }
    
    
    public function compileComponent(
    string $template
): string {

    /*
     * Components themselves must not recursively
     * invoke the component compiler again.
     */

    /*
     * PRANCHI comments
     */
    $template = preg_replace(
        "/\{\{\-\-(.*?)\-\-\}\}/s",
        "",
        $template
    );

    /*
     * @php
     */
    if ($this->allowPhpBlocks) {

        $template = preg_replace_callback(
            "/@php(.*?)@endphp/s",
            function ($match) {

                return "<?php " .
                    trim($match[1]) .
                    " ?>";
            },
            $template
        );
    }

    /*
     * @props
     */
    $template = preg_replace_callback(
        '/@props\((.*?)\)/s',
        function ($match) {

            return
                "<?php\n" .
                '$__defaults = ' .
                $match[1] .
                ";\n" .

                "foreach ((array) \$__defaults as \$__key => \$__default) {\n" .

                "    if (!isset(\$__props[\$__key])) {\n" .

                "        \$__props[\$__key] = \$__default;\n" .

                "    }\n" .

                "    \${\$__key} = \$__props[\$__key];\n" .

                "}\n" .

                "?>";
        },
        $template
    );

    /*
     * RAW
     */
    if ($this->allowRawOutput) {

        $template = preg_replace(
            "/\{!!\s*(.*?)\s*!!\}/s",
            '<?php echo $1; ?>',
            $template
        );
    }

    /*
     * SAFE OUTPUT
     */
    $template = preg_replace_callback(
        "/\{\{\s*(.*?)\s*\}\}/s",
        function ($match) {

            $expression = trim(
                $match[1]
            );

            if ($expression === '') {
                return '';
            }

            return
                "<?php echo e(" .
                $expression .
                "); ?>";
        },
        $template
    );

    /*
     * IF
     */
    $template = preg_replace_callback(
        "/@if\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/",
        fn($m) =>
            "<?php if(" .
            $m[1] .
            "): ?>",
        $template
    );

    /*
     * ELSEIF
     */
    $template = preg_replace_callback(
        "/@elseif\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/",
        fn($m) =>
            "<?php elseif(" .
            $m[1] .
            "): ?>",
        $template
    );

    /*
     * ELSE
     */
    $template = preg_replace(
        "/@else\b/",
        "<?php else: ?>",
        $template
    );

    /*
     * ENDIF
     */
    $template = preg_replace(
        "/@endif\b/",
        "<?php endif; ?>",
        $template
    );

    /*
     * FOREACH
     */
    $template = preg_replace(
        "/@foreach\s*\((.*?)\)/",
        "<?php foreach($1): ?>",
        $template
    );

    /*
     * ENDFOREACH
     */
    $template = preg_replace(
        "/@endforeach/",
        "<?php endforeach; ?>",
        $template
    );

    /*
     * CSRF
     */
    $template = preg_replace(
        "/@csrf\b/",
        "<?= csrf_field() ?>",
        $template
    );

    return $template;
}


    /* =========================================================
     | SECURITY HELPERS
     ========================================================= */

    protected function validateViewName(
        string $view
    ): void {

        if ($view === "") {
            throw new \InvalidArgumentException(
                "View name cannot be empty."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Block absolute paths
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $view,
                "/"
            ) &&
            str_starts_with(
                $view,
                "/"
            )
        ) {

            throw new \InvalidArgumentException(
                "Absolute view paths are not allowed."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Block Windows absolute paths
        |--------------------------------------------------------------------------
        */

        if (
            preg_match(
                '/^[A-Za-z]:[\\\\\/]/',
                $view
            )
        ) {

            throw new \InvalidArgumentException(
                "Absolute view paths are not allowed."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Block traversal
        |--------------------------------------------------------------------------
        */

        if (
            str_contains(
                $view,
                ".."
            )
        ) {

            throw new \InvalidArgumentException(
                "Path traversal is not allowed."
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Only safe characters
        |--------------------------------------------------------------------------
        */

        if (
            !preg_match(
                '/^[A-Za-z0-9_.\/-]+$/',
                $view
            )
        ) {

            throw new \InvalidArgumentException(
                "Invalid view name."
            );
        }
    }


    protected function isPathInside(
        string $path,
        string $base
    ): bool {

        $path =
            rtrim(
                str_replace(
                    "\\",
                    "/",
                    $path
                ),
                "/"
            );


        $base =
            rtrim(
                str_replace(
                    "\\",
                    "/",
                    $base
                ),
                "/"
            );


        return
            $path === $base ||
            str_starts_with(
                $path,
                $base . "/"
            );
    }


    /* =========================================================
     | COMPONENT COMPILER
     ========================================================= */

 public function setComponentCompiler(
    ComponentCompiler $compiler
): void {

    $this->componentCompiler = $compiler;

    /*
     * Give the renderer access to the current
     * PRANCHI instance so component templates
     * can use the same compiler pipeline.
     */
    $compiler
        ->getRenderer()
        ->setPranchi($this);
}


    public function getComponentCompiler():
        ?ComponentCompiler
    {

        return $this->componentCompiler;
    }
}
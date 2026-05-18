<?php

use App\Core\Components\ComponentManager;
use App\Core\Components\ComponentRenderer;
use App\Core\Components\ComponentCompiler;

/* =========================
   GET PRANCHI INSTANCE
========================= */

$pranchi = $GLOBALS['pranchi'] ?? null;

if (!$pranchi) {
    throw new RuntimeException(
        "Pranchi instance not found. Ensure index.php sets \$GLOBALS['pranchi']"
    );
}

/* =========================
   INIT COMPONENT SYSTEM
========================= */

$manager  = new ComponentManager();
$renderer = new ComponentRenderer();
$compiler = new ComponentCompiler($manager, $renderer);

/* attach compiler */
if (method_exists($pranchi, 'setComponentCompiler')) {
    $pranchi->setComponentCompiler($compiler);
} else {
    throw new RuntimeException("Pranchi does not support component compiler injection");
}

/* =========================
   BASE PATH SAFE BUILD
========================= */

clearstatcache(true);

$base = rtrim(base_path(), DIRECTORY_SEPARATOR);
$componentPath = $base . DIRECTORY_SEPARATOR . 'views/components';

if (!is_dir($componentPath)) {
    throw new RuntimeException("Components directory not found: {$componentPath}");
}

/* =========================
   OPTIONAL: CLEAR OLD CACHE
========================= */

if (method_exists($manager, 'clear')) {
    $manager->clear();
}

/* =========================
   AUTO COMPONENT LOADER
========================= */

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($componentPath, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {

    if (!$file->isFile()) {
        continue;
    }

    $filename = $file->getFilename();

    // only .pra.php files
    if (substr($filename, -8) !== '.pra.php') {
        continue;
    }

    $fullPath = $file->getPathname();

    // relative path from components folder
    $relative = str_replace(
        $componentPath . DIRECTORY_SEPARATOR,
        '',
        $fullPath
    );

    /**
     * NAME GENERATION RULE
     * forms/input.pra.php → forms-input
     * button.pra.php → button
     */
    $name = str_replace('.pra.php', '', $relative);
    $name = str_replace(['/', '\\'], '-', $name);

    $manager->register($name, $fullPath);
}

/* =========================
   DONE
========================= */

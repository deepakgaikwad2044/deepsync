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
   COMPONENT SYSTEM INIT
========================= */

$manager  = new ComponentManager();
$renderer = new ComponentRenderer();
$compiler = new ComponentCompiler($manager, $renderer);

/* =========================
   SAFE BASE PATH HELPER
========================= */

$base = rtrim(base_path(), '/');

/* =========================
   REGISTER COMPONENTS
========================= */

$components = [
    'button' => $base . '/views/components/button.pra.php',
    'alert'  => $base . '/views/components/alert.pra.php',
    'card'  => $base . '/views/components/card.pra.php',
    'navbar'  => $base . '/views/components/navbar.pra.php',
    'dashboard'  => $base . '/views/components/dashboard.pra.php',
    
];

/* validate paths before register */
foreach ($components as $name => $path) {
    if (!file_exists($path)) {
        throw new RuntimeException("Component view not found: {$name} => {$path}");
    }

    $manager->register($name, $path);
}

/* =========================
   ATTACH TO PRANCHI CORE
========================= */

if (method_exists($pranchi, 'setComponentCompiler')) {
    $pranchi->setComponentCompiler($compiler);
} else {
    throw new RuntimeException("Pranchi does not support component compiler injection");
}
<?php

use App\Core\Components\ComponentManager;
use App\Core\Components\ComponentRenderer;
use App\Core\Components\ComponentCompiler;

/* =========================
   GET GLOBAL PRANCHI
========================= */
$pranchi = $GLOBALS['pranchi'] ?? null;

/* safety check (VERY IMPORTANT) */
if (!$pranchi) {
    die("Pranchi instance not found. Make sure index.php sets GLOBALS['pranchi']");
}

/* =========================
   COMPONENT SYSTEM
========================= */

$manager = new ComponentManager();
$renderer = new ComponentRenderer();
$compiler = new ComponentCompiler($manager, $renderer);

/* =========================
   REGISTER COMPONENTS
========================= */

$manager->register(
    'button',
    base_path('/views/components/button.pra.php')
);

$manager->register(
    'alert',
    base_path('/views/components/alert.pra.php')
);

/* =========================
   ATTACH TO PRANCHI
========================= */

$pranchi->setComponentCompiler($compiler);
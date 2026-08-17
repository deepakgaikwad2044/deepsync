<?php

namespace App\Core\Components;

class ComponentRenderer
{
   public function render(string $viewPath, array $props = [], string $slot = ''): string
{
    if (!file_exists($viewPath)) {
        return "<!-- Component not found -->";
    }

    $content = file_get_contents($viewPath);

    $compiler = $GLOBALS['pranchi']->getComponentCompiler();

    // ONLY compile blade ONCE
    $content = $compiler->compileBlade($content);

    $__props = $props;
    $__slot  = $slot;

    ob_start();
    eval('?>' . $content);
    return ob_get_clean();
}
}
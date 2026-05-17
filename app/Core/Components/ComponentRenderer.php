<?php

namespace App\Core\Components;

class ComponentRenderer
{
    public function render(string $viewPath, array $props = [], string $slot = ''): string
    {
        if (!file_exists($viewPath)) {
            return "<!-- Component not found: {$viewPath} -->";
        }

        extract($props);

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
}
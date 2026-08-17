<?php

namespace App\Core\Components;

class ComponentCompiler
{
    protected ComponentManager $manager;
    protected ComponentRenderer $renderer;

    protected array $stack = [];

    public function __construct(
        ComponentManager $manager,
        ComponentRenderer $renderer
    ) {
        $this->manager  = $manager;
        $this->renderer = $renderer;
    }

    public function compile(string $content): string
    {
        // STEP 1: compile components FIRST (no blade yet)
        $content = $this->compileComponents($content);

        // STEP 2: THEN compile blade globally
        return $this->compileBlade($content);
    }

    private function compileComponents(string $content): string
    {
        // SELF CLOSING
        $content = preg_replace_callback(
            '/<x-([\w\-.]+)\s*([^>]*)\/>/s',
            fn($m) => $this->renderComponent($m[1], $m[2], ''),
            $content
        );

        // NORMAL TAGS
        $content = preg_replace_callback(
            '/<x-([\w\-.]+)\s*([^>]*)>(.*?)<\/x-\1>/s',
            fn($m) => $this->renderComponent($m[1], $m[2], $m[3]),
            $content
        );

        return $content;
    }

    private function renderComponent(string $name, string $attributes, string $slot): string
    {
        if (in_array($name, $this->stack)) {
            return "<!-- recursion blocked: {$name} -->";
        }

        $this->stack[] = $name;

        $view = $this->manager->resolve($name);

        if (!$view) {
            array_pop($this->stack);
            return "<!-- component not found: {$name} -->";
        }

        $props = $this->parseAttributes($attributes);

        // 🔥 IMPORTANT: ONLY component compile in slot (NO blade here)
        if ($slot !== '') {
            $slot = $this->compileComponents($slot);
        }

        $output = $this->renderer->render($view, $props, $slot);

        array_pop($this->stack);

        return $output;
    }

    private function parseAttributes(string $attributes): array
    {
        $props = [];

        preg_match_all(
            '/([\w\-:]+)\s*=\s*(["\'])(.*?)\2/',
            $attributes,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $props[$m[1]] = $m[3];
        }

        return $props;
    }

    public function compileBlade(string $content): string
    {
        $content = preg_replace('/\{!!\s*(.*?)\s*!!\}/s', '<?= $1 ?>', $content);
        $content = preg_replace('/\{\{\s*(.*?)\s*\}\}/s', '<?= e($1) ?>', $content);

        $content = preg_replace('/@php(.*?)@endphp/s', '<?php$1?>', $content);

        $content = preg_replace('/@if\s*\((.*?)\)/', '<?php if ($1): ?>', $content);
        $content = preg_replace('/@elseif\s*\((.*?)\)/', '<?php elseif ($1): ?>', $content);
        $content = str_replace('@else', '<?php else: ?>', $content);
        $content = str_replace('@endif', '<?php endif; ?>', $content);

        $content = preg_replace('/@foreach\s*\((.*?)\)/', '<?php foreach ($1): ?>', $content);
        $content = str_replace('@endforeach', '<?php endforeach; ?>', $content);

        return $content;
    }
}
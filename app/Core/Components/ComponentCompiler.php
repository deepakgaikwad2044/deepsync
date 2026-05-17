<?php

namespace App\Core\Components;

class ComponentCompiler
{
    protected ComponentManager $manager;
    protected ComponentRenderer $renderer;

    public function __construct(
        ComponentManager $manager,
        ComponentRenderer $renderer
    ) {
        $this->manager  = $manager;
        $this->renderer = $renderer;
    }

public function compile(string $content): string
{
    return preg_replace_callback(
        '/<x-([\w\-]+)([^>]*)>(.*?)<\/x-\1>/s',
        function ($match) {

            $name = $match[1];
            $attributes = $match[2];
            $slot = $match[3];

            $view = $this->manager->resolve($name);

            if (!$view) {
                return $match[0]; // fallback
            }

            preg_match_all('/(\w+)="(.*?)"/', $attributes, $attrMatches);

            $props = [];
            foreach ($attrMatches[1] as $i => $key) {
                $props[$key] = $attrMatches[2][$i];
            }

            return $this->renderer->render($view, $props, $slot);
        },
        $content
    );
}

}
<?php

declare(strict_types=1);

namespace App\Core\Components;

use RuntimeException;

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

    public function compile(
        string $content
    ): string {

        return $this->compileComponents(
            $content
        );
    }

    protected function compileComponents(
        string $content
    ): string {

        /*
         * Self closing:
         *
         * <x-button type="success" />
         */
        $content = preg_replace_callback(
            '/<x-([\w\-.]+)\s*([^>]*)\/>/s',
            function ($match) {

                return $this->renderComponent(
                    $match[1],
                    $match[2],
                    ''
                );
            },
            $content
        );

        /*
         * Normal component:
         *
         * <x-button>Save</x-button>
         */
        $content = preg_replace_callback(
            '/<x-([\w\-.]+)\s*([^>]*)>(.*?)<\/x-\1>/s',
            function ($match) {

                return $this->renderComponent(
                    $match[1],
                    $match[2],
                    $match[3]
                );
            },
            $content
        );

        return $content;
    }

    protected function renderComponent(
        string $name,
        string $attributes,
        string $slot
    ): string {

        if (in_array(
            $name,
            $this->stack,
            true
        )) {
            throw new RuntimeException(
                "Recursive component detected: {$name}"
            );
        }

        $this->stack[] = $name;

        try {

            $view = $this->manager->resolve(
                $name
            );

            if ($view === null) {
                throw new RuntimeException(
                    "Component not found: {$name}"
                );
            }

            $props =
                $this->parseAttributes(
                    $attributes
                );

            /*
             * Nested components inside slot.
             */
            if ($slot !== '') {
                $slot =
                    $this->compileComponents(
                        $slot
                    );
            }

            /*
             * IMPORTANT:
             *
             * Generate a normal PHP call.
             * No eval().
             */
            return
                '<?= $__componentRenderer->render(' .
                var_export($view, true) .
                ', ' .
                var_export($props, true) .
                ', ' .
                var_export($slot, true) .
                ') ?>';

        } finally {

            array_pop($this->stack);
        }
    }

    protected function parseAttributes(
        string $attributes
    ): array {

        $props = [];

        preg_match_all(
            '/([\w\-:]+)\s*=\s*(["\'])(.*?)\2/s',
            $attributes,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {

            $key = $match[1];

            /*
             * Block dangerous PHP-looking
             * attribute names.
             */
            if (!preg_match(
                '/^[A-Za-z0-9_:\-]+$/',
                $key
            )) {
                continue;
            }

            $props[$key] = $match[3];
        }

        return $props;
    }

    public function getRenderer(): ComponentRenderer
    {
        return $this->renderer;
    }
}
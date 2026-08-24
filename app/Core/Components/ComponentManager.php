<?php

declare(strict_types=1);

namespace App\Core\Components;

use InvalidArgumentException;
use RuntimeException;

class ComponentManager
{
    protected array $components = [];

    protected string $basePath;

    public function __construct(
        ?string $basePath = null
    ) {
        $this->basePath = rtrim(
            $basePath ?? base_path("views/components"),
            DIRECTORY_SEPARATOR
        );
    }

    public function register(
        string $name,
        string $viewPath
    ): void {

        $name = trim($name);

        if ($name === '') {
            throw new InvalidArgumentException(
                "Component name cannot be empty."
            );
        }

        /*
         * Component names:
         *
         * button
         * alert
         * form.input
         * user-card
         */
        if (!preg_match(
            '/^[A-Za-z0-9_.-]+$/',
            $name
        )) {
            throw new InvalidArgumentException(
                "Invalid component name."
            );
        }

        $realBase = realpath($this->basePath);

        if ($realBase === false) {
            throw new RuntimeException(
                "Component directory does not exist."
            );
        }

        $realPath = realpath($viewPath);

        if ($realPath === false) {
            throw new RuntimeException(
                "Component view not found: {$viewPath}"
            );
        }

        if (!$this->isPathInside(
            $realPath,
            $realBase
        )) {
            throw new RuntimeException(
                "Component path is outside the component directory."
            );
        }

        if (!is_file($realPath)) {
            throw new RuntimeException(
                "Component view is not a file."
            );
        }

        if (
            !str_ends_with(
                strtolower($realPath),
                '.pra.php'
            )
        ) {
            throw new RuntimeException(
                "Only .pra.php component views are allowed."
            );
        }

        $this->components[$name] = $realPath;
    }

    public function resolve(
        string $name
    ): ?string {
        return $this->components[$name] ?? null;
    }

    public function all(): array
    {
        return $this->components;
    }

    protected function isPathInside(
        string $path,
        string $base
    ): bool {

        $path = rtrim(
            str_replace('\\', '/', $path),
            '/'
        );

        $base = rtrim(
            str_replace('\\', '/', $base),
            '/'
        );

        return $path === $base ||
            str_starts_with(
                $path,
                $base . '/'
            );
    }
}
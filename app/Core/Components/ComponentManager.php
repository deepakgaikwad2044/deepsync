<?php

namespace App\Core\Components;

class ComponentManager
{
    protected array $components = [];

    public function register(string $name, string $viewPath)
    {
        $this->components[$name] = $viewPath;
    }

    public function resolve(string $name): ?string
    {
        return $this->components[$name] ?? null;
    }

    public function all(): array
    {
        return $this->components;
    }
}
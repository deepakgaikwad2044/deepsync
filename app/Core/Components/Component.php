<?php

namespace App\Core\Components;

abstract class Component
{
    protected array $props = [];
    protected string $slot = '';

    public function __construct(array $props = [], string $slot = '')
    {
        $this->props = $props;
        $this->slot  = $slot;
    }

    public function prop(string $key, $default = null)
    {
        return $this->props[$key] ?? $default;
    }

    public function slot(): string
    {
        return $this->slot;
    }

    abstract public function render(): string;
}
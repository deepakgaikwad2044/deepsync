@php
$type = $__props['type'] ?? 'primary';
$slot = $__slot ?? '';
$text = $slot !== ''
    ? $slot
    : ($__props['text'] ?? 'Button');
@endphp

<button class="btn btn-{{ $type }}">
    {{ $text }}
</button>
@php
$slot = $__slot ?? '';
$type = $__props['type'] ?? 'primary';
$text = $slot ?: ($__props['text'] ?? 'Button');
@endphp

<button class="component_btn component_btn_{{ $type }}">
    {!! trim($text) !!}
</button>
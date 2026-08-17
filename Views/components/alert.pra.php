@php
$type = $__props['type'] ?? 'info';
$text = $__slot ?: ($__props['message'] ?? '');
@endphp

<div class="component_alert component_alert_{{ $type }}">
    {!! trim($text) !!}
</div>
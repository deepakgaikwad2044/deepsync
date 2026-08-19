@php
$title = $__props['title'] ?? '';
$footer = $__props['footer'] ?? '';
$slot = $__slot ?? '';
@endphp

<div class="component_card">

    @if($title)
        <div class="component_card_header">
            <h3>{{ $title }}</h3>
        </div>
    @endif

    <div class="component_card_body">
        {!! $slot !!}
    </div>

    @if($footer)
        <div class="component_card_footer">
            {{ $footer }}
        </div>
    @endif

</div>
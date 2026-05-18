@php
$brand = "pranchi";
$slot = $__slot ?? '';
@endphp

<nav class="component_navbar">
    <div class="component_navbar_brand">
        {{ $brand }}
    </div>

    <div class="component_navbar_menu">
        {!! $slot !!}
    </div>
</nav>
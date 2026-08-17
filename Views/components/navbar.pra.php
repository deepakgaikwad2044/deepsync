<style>
  .component_navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: #1f2937;
    color: white;
}

.component_navbar_menu a {
    margin-left: 10px;
    color: #cbd5e1;
    text-decoration: none;
}

.component_navbar_menu a:hover {
    color: white;
}

.component_navbar_brand {
    font-weight: bold;
    font-size: 18px;
}
</style>

<nav class="component_navbar">

    <div class="component_navbar_brand">
        {{ $brand ?? 'pranchi' }}
    </div>

    <div class="component_navbar_menu">
        {!! $slot !!}
    </div>

</nav>
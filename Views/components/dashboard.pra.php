<!-- views/components/dashboard-demo.pra.php -->

@php
    $users = [
        ['name' => 'Rahul'],
        ['name' => 'Priya'],
        ['name' => 'Amit'],
    ];

    $loggedIn = true;
@endphp

<x-card title="Directive Demo" footer="Pranchi Blade Directives">

    @if($loggedIn)
        <x-alert type="success">
            User is logged in.
        </x-alert>
    @else
        <x-alert type="error">
            User is not logged in.
        </x-alert>
    @endif

    <h4>User List</h4>

    <ul>
        @foreach($users as $user)
            <li>{{ $user['name'] }}</li>
        @endforeach
    </ul>

    <x-button type="primary">
        Continue
    </x-button>

</x-card>
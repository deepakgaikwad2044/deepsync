@extends('layout')

@section('content')

<h1>Hello {{ $name }}</h1>

@if($name)
User exists 😎
@else
No user 😢
@endif

@foreach($users as $user)
{{ $user }}
@endforeach

@endsection
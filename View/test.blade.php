@extends('layout')

@section('content')

<h1> safe: {{ $name }}</h1>

<h1> Raw :{!! $name !!} </h1>
  
  
 @if($user)
Welcome {{ $user }}
@endif

@foreach($users as $user)
{{ $user }}
@endforeach

@endsection
@extends('base')
@section('content')
<p class="text-xl mt-4">Hello, {{ Auth::user()->name }}</p>

@endsection
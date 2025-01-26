@extends('base')
@section('content')
Hello, {{ Auth::user()->name }}
@endsection
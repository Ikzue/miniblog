@extends('base')

@section('content')
@if ($errors->any())
<div>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<form action="{{ route('posts.store') }}" method="POST">
    @csrf
    <label for="title">Title:</label>
    <input type="text" id="title" name="title">
    <label for="content">Content:</label>
    <textarea type="text" id="content" name="content"></textarea>
    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
    <button>Save</button>
</form>
@endsection


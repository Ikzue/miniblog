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
<form class="flex flex-col" action="{{ route('posts.store') }}" method="POST">
    @csrf
    <label class="font-medium" for="title">Title </label>
    <input class="border-gray-400 rounded" type="text" id="title" name="title">
    <label class="font-medium" for="content">Content </label>
    <textarea class="border-gray-400 rounded" rows=4 type="text" id="content" name="content"></textarea>
    <button class="btn mt-4 max-w-20">Save</button>
</form>
@endsection


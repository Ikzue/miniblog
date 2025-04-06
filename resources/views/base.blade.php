<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>my-blog</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="mx-8 my-4">
    <h1 class="text-4xl font-bold text-blue-800">
        <a href="/">
          Miniblog
        </a>
    </h1>
    <hr>
    <nav class="my-1">
        {{ Auth::user()->name }} ({{ Auth::user()->role }}) - 
        <a href="{{ route('posts.list.ui') }}">Posts</a>
        - <a href="{{ route('comments.list.ui') }}">My comments</a>
        @can('viewAny', App\Models\User::class)
        - <a href="{{ route('users.list.ui') }}">Users</a>
        @endcan
    </nav>
    <hr>
    @section('content')
    @show
    
    @section('script')
    @show
</body>
</html>
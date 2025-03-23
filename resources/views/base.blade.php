<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <a href="/">Home</a> - 
        <a href="{{ route('posts.list.ui') }}">Posts</a> -
        <a href="{{ route('comments.list.ui') }}">My comments</a>
    </nav>
    <hr>
    @section('content')
    @show
    
    @section('script')
    @show
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>my-blog</title>
</head>
<body>
    <nav>
        <a href="{{ route('posts.list.ui') }}">Posts</a>
        <a href="{{ route('comments.list.ui') }}">Comments</a>
    </nav>
    <hr>
    @section('content')
    @show
    
    @section('script')
    @show
</body>
</html>
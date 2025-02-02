<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Post;


class PostController extends Controller
{
    // GET posts
    public function index()
    {
        return Post::query()->orderByDesc('created_at')->get();
    }


    // POST posts - Create
    public function store(Request $request)
    {
        
        $input = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'user_id' => 'missing',
        ]);
        $request->user()->posts()->create($input);
    
        return redirect()->route('posts.list.ui')->with('success', 'Post created successfully');
    }

    // GET posts/{id} - Show
    public function show(Post $post)
    {
        return $post;
    }

    // PUT/PATCH posts/{id}
    public function update(Request $request, $post)
    {
        if ($request->method() != 'PUT')
        {
            return Response::json(['error' => "Incorrect method"], 405);
        }
        $input = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'user_id' => 'missing',
        ]);


        $post->fill($input)->save();
        return $post;
    }

    // DELETE posts/{id}
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['message' => 'Post deleted'], 204);
    }
}

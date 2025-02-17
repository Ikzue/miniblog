<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;


class PostController extends Controller
{
    // GET posts
    public function index()
    {
        return Post::with('user:id,name')->orderByDesc('created_at')->get();
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
    public function show(Request $request, Post $post)
    {
        $post->is_own_post = $post->user_id == $request->user()->id;
        return $post;
    }

    // PUT/PATCH posts/{id}
    public function update(Request $request, Post $post)
    {
        if ($request->method() != 'PUT')
        {
            return response()->json(['error' => "Incorrect method"], 405);
        }
        else if ($request->user()->id != $post->user_id)
        {
            return response()->json(['error' => "Access denied"], 403);
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
    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->id != $post->user_id)
        {
            return response()->json(['error' => "Access denied"], 403);
        }
        $post->comments()->delete();
        $post->delete();
        return response()->json(['message' => 'Post deleted'], 204);
    }
}

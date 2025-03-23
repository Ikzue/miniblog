<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    // GET posts
    public function index()
    {
        $postsList = Post::with('user:id,name')->orderByDesc('created_at')->get();
        return PostResource::collection($postsList);
    }

    // POST posts - Create
    public function store(Request $request)
    {
        $this->authorize('create', Post::class);
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
        return new PostResource($post);
    }

    // PUT/PATCH posts/{id}
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        if ($request->method() != 'PUT')
        {
            return response()->json(['error' => "Incorrect method"], 405);
        }

        $input = $request->validate([
            'title' => 'required',
            'content' => 'required',
            'user_id' => 'missing',
        ]);

        $post->fill($input)->save();
        return new PostResource($post);
    }

    // DELETE posts/{id}
    public function destroy(Request $request, Post $post)
    {
        $this->authorize('delete', $post);
        $post->comments()->delete();
        $post->delete();
        return response()->json(['message' => 'Post deleted'], 204);
    }
}

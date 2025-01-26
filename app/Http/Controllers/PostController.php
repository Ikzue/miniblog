<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    protected $keys = [
        'title',
        'content',
        'user_id'
    ];

    protected $post_put_rules = [
        'title' => 'required',
        'content' => 'required',
        'user_id' => 'required|exists:users,id'
    ];

    protected $patch_rules = [
        'title' => 'filled',
        'content' => 'filled',
        'user_id' => 'filled|exists:users,id',
    ];


    // GET posts/create - Display creation form
    public function create()
    {
        return array_fill_keys($this->keys, '');
    }

    // POST posts - Create
    public function store(Request $request)
    {
        $input = $request->validate($this->post_put_rules);
    
        $post = new Post;
        foreach($this->keys as $key){
            $post->$key = $input[$key];
        }
        $post->save();
    
        return redirect()->route('posts.index')->with('success', 'Post created successfully');
    }

    // GET posts - Show all
    public function index()
    {
        return Post::query()->orderByDesc('created_at')->get();
    }

    // GET posts/{id} - Show
    public function show(string $id)
    {
        return Post::find($id);
    }

    // GET posts/{id}/edit - Show edition form
    public function edit(string $id)
    {
        $post = Post::find($id);
        return $post->makeHidden('id', 'created_at', 'updated_at');
    }

    // PUT/PATCH posts/{id}
    public function update(Request $request, string $id)
    {
        if ($request->method() == 'PUT')
        {
            $input = $request->validate($this->post_put_rules);
        }
        else {
            $input = $request->validate($this->patch_rules);
        }

        $post = Post::find($id);
        foreach($this->keys as $key){
            if (array_key_exists($key, $input)){
                $post->$key = $input[$key];
            }
        }
        $post->save();
        return $post;
    }

    // DELETE posts/{id}
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if(! $post){
            return ['message' => "Post couldn't be found"];
        }
        $post->delete();
        return ['message' => 'Post deleted'];
    }
}

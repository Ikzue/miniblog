<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    protected $keys = [
        "title",
        "content",
        "user_id"
    ];


    // GET posts
    public function index()
    {
        return Post::all();
    }

    // GET posts/create - Display creation form
    public function create()
    {
        return [
            "title" => "",
            "content" => "",
            "user_id" => "",
        ];
    }

    // POST posts - Create
    public function store(Request $request)
    {
        try {
            $input_form = $this->get_input_form($request, True);
        }
        catch (Exception $e) {
            return ["message" => "Error in input: " . $e->getMessage()];
        }

        $post = new Post;
        foreach($this->keys as $key){
            $post->$key = $input_form[$key];
        }
        $post->save();
        return $post;
    }

    // GET posts/{id} - Show object
    public function show(string $id)
    {
        return Post::find($id);
    }

    // GET posts/{id} - Display edition form
    public function edit(string $id)
    {
        $post = Post::find($id);
        return $post;
    }

    // PUT/PATCH posts/{id}
    public function update(Request $request, string $id)
    {
        try {
            if ($request->method() == "PUT")
            {
                $input_form = $this->get_input_form($request, True);
            }
            else {
                $input_form = $this->get_input_form($request);
            }
        }
        catch (Exception $e) {
            return ["message" => "Error in input: " . $e->getMessage()];
        }

        $post = Post::find($id);
        foreach($this->keys as $key){
            if (array_key_exists($key, $input_form)){
                $post->$key = $input_form[$key];
            }
        }
        $post->save();
        return $post;
    }

    // DELETE posts/{id}
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if(!$post){
            return ["message" => "Post couldn't be found"];
        }
        else {
            $post->delete();
            return ["message" => "Post deleted"];
        }
    }

    private function get_input_form($request, $check_missing = false) {
        $body = $request->getContent();
        if (!json_validate($body)){
            throw new Exception("Please input valid JSON.");
        }
        $input_form = json_decode($body, true);
    
        if ($check_missing) {
            $missing = [];
            foreach($this->keys as $key)
                if(!array_key_exists($key, $input_form))
                    array_push($missing, $key);
            if ($missing)
                throw new Exception("Missing fields: " .implode(", ", $missing));    
        }

        return $input_form;
    }
}

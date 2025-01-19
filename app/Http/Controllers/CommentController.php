<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    protected $keys = [
        "content",
        "user_id",
        "post_id"
    ];


    // GET comments
    public function index()
    {
        return Comment::all();
    }

    // GET comments/create - Display creation form
    public function create()
    {
        return [
            "content" => "",
            "user_id" => "",
            "post_id" => "",
        ];
    }

    // POST comments - Create
    public function store(Request $request)
    {
        try {
            $input_form = $this->get_input_form($request, True);
        }
        catch (Exception $e) {
            return ["message" => "Error in input: " . $e->getMessage()];
        }

        $comment = new Comment;
        foreach($this->keys as $key){
            $comment->$key = $input_form[$key];
        }
        $comment->save();
        return $comment;
    }

    // GET comments/{id} - Show object
    public function show(string $id)
    {
        return Comment::find($id);
    }

    // GET comments/{id} - Display edition form
    public function edit(string $id)
    {
        $comment = Comment::find($id);
        return $comment;
    }

    // PUT/PATCH comments/{id}
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

        $comment = Comment::find($id);
        foreach($this->keys as $key){
            if (array_key_exists($key, $input_form)){
                $comment->$key = $input_form[$key];
            }
        }
        $comment->save();
        return $comment;
    }

    // DELETE comments/{id}
    public function destroy(string $id)
    {
        $comment = Comment::find($id);
        if(!$comment){
            return ["message" => "Comment couldn't be found"];
        }
        else {
            $comment->delete();
            return ["message" => "Comment deleted"];
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
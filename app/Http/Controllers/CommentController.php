<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    protected $keys = [
        'content',
        'user_id',
        'post_id'
    ];

    protected $post_put_rules = [
        'content' => 'required',
        'user_id' => 'required|exists:users,id',
        'post_id' => 'required|exists:posts,id'
    ];

    protected $patch_rules = [
        'content' => 'filled',
        'user_id' => 'filled|exists:users,id',
        'post_id' => 'filled|exists:posts,id'
    ];


    // GET comments/create - Display creation form
    public function create()
    {
        return array_fill_keys($this->keys, '');
    }

    // POST comments - Create
    public function store(Request $request)
    {
        $input = $request->validate($this->post_put_rules);
        $comment = new Comment;
        foreach($this->keys as $key){
            $comment->$key = $input[$key];
        }
        $comment->save();
        return $comment;
    }

    // GET comments - Show list of comments
    public function index(Request $request)
    {
        $comments = Comment::query();
        $post_id = $request->query('post_id');
        if ($post_id) {
            $comments = $comments->where('post_id', $post_id);
        }
        return $comments->orderByDesc('created_at')->with('user:id,name')->get();
    }

    // GET comments/{id} - Show comment
    public function show(string $id)
    {
        return Comment::find($id);
    }

    // GET comments/{id}/edit - Show edition form
    public function edit(string $id)
    {
        $comment = Comment::find($id);
        return $comment->makeHidden('id', 'created_at', 'updated_at');
    }

    // PUT/PATCH comments/{id}
    public function update(Request $request, string $id)
    {
        if ($request->method() == 'PUT')
        {
            $input = $request->validate($this->post_put_rules);
        }
        else {
            $input = $request->validate($this->patch_rules);
        }


        $comment = Comment::find($id);
        foreach($this->keys as $key){
            if (array_key_exists($key, $input)){
                $comment->$key = $input[$key];
            }
        }
        $comment->save();
        return $comment;
    }

    // DELETE comments/{id}
    public function destroy(string $id)
    {
        $comment = Comment::find($id);
        if(! $comment){
            return ['message' => "Comment couldn't be found"];
        }
        $comment->delete();
        return ['message' => 'Comment deleted'];
    }
}
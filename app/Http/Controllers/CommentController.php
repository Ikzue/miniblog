<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Comment;

class CommentController extends Controller
{
    // GET comments 
    public function index(Request $request)
    {
        $comments = Comment::query();
        $post_id = $request->query('post_id');
        if ($post_id) {
            $comments = $comments->where('post_id', $post_id);
        }
        return $comments->orderByDesc('created_at')->with('user:id,name')->get();
    }


    // POST comments - Create
    public function store(Request $request)
    {
        $input = $request->validate([
            'content' => 'required',
            'post_id' => 'required',
            'user_id' => 'missing',
        ]);
        $request->user()->comments()->create($input);
        return response()->json(['message' => 'Comment created'], 201);
    }

    // GET comments/{id} - Show comment
    public function show(Comment $comment)
    {
        return $comment;
    }

    // PUT/PATCH comments/{id}
    public function update(Request $request, Comment $comment)
    {
        if ($request->method() != 'PUT')
        {
            return Response::json(['error' => 'Incorrect method'], 405);
        }
        $input = $request->validate([
            'content' => 'required',
            'post_id' => 'missing',
            'user_id' => 'missing',
        ]);

        $comment->fill($input)->save();
        return $comment;
    }

    // DELETE comments/{id}
    public function destroy(comment $comment)
    {
        $comment->delete();
        return response()->json(['message' => 'Comment deleted'], 204);
    }
}
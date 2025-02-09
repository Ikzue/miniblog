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
        $post_id = $request->query('post_id') ?? null;  // Post details page
        $user_id = $request->query('my_comments') == 'true' ? $request->user()->id : null;  // User's comments page
        if ($post_id && $user_id)
        {
            return Response::json(['error' => "Choose to show either a post's comments or a user's comments"], 400);
        }

        return Comment::with(['user:id,name', 'post:id,title'])
            ->when($post_id, function($query) use ($post_id) { $query->where('post_id', $post_id); })
            ->when($user_id, function($query) use ($user_id) { $query->where('user_id', $user_id); })
            ->orderByDesc('created_at')
            ->get();
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
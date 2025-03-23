<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Post;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    // GET comments 
    public function index(Request $request)
    {
        $post_id = $request->query('post_id') ?? null;  // Post details page
        $user_id = $request->query('my_comments') == 'true' ? $request->user()->id : null;  // User's comments page
        if ($post_id && $user_id)
        {
            return response()->json(['error' => "Choose to show either a post's comments or a user's comments"], 400);
        }

        $commentsList = Comment::with(['user:id,name', 'post:id,title'])
            ->when($post_id, fn ($query) => $query->where('post_id', $post_id))
            ->when($user_id, fn($query) => $query->where('user_id', $user_id))
            ->orderByDesc('created_at')
            ->get();
        return CommentResource::collection($commentsList);
    }


    // POST comments - Create
    public function store(Request $request)
    {
        $input = $request->validate([
            'content' => 'required',
            'post_id' => 'required',
            'user_id' => 'missing',
        ]);
        $this->authorize('canComment', Post::findOrFail($input['post_id']));

        $request->user()->comments()->create($input);
        return response()->json(['message' => 'Comment created'], 201);
    }

    // GET comments/{id} - Show comment
    public function show(Comment $comment)
    {
        return new CommentResource($comment);
    }

    // PUT/PATCH comments/{id}
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        if ($request->method() != 'PUT')
        {
            return response()->json(['error' => 'Incorrect method'], 405);
        }
        else if ($request->user()->id != $comment->user_id)
        {
            return response()->json(['error' => "Access denied"], 403);
        }
        $input = $request->validate([
            'content' => 'required',
            'post_id' => 'missing',
            'user_id' => 'missing',
        ]);

        $comment->fill($input)->save();
        return new CommentResource($comment);
    }

    // DELETE comments/{id}
    public function destroy(Request $request, Comment $comment)
    {
        $this->authorize('delete', $comment);
        if ($request->user()->id != $comment->user_id)
        {
            return response()->json(['error' => "Access denied"], 403);
        }
        $comment->delete();
        return response()->json(['message' => 'Comment deleted'], 204);
    }
}
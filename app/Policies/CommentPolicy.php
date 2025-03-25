<?php

namespace App\Policies;


use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Enums\Role;

class CommentPolicy
{
    public function before(User $user,  string $ability)
    {
        if ($user->role === Role::MODERATOR->value) {
            return true;
        }
        return null;
    }

    // Writers need to be post, readers can comment on all posts
    public function create(User $user, Post $post): bool
    {
        if ($user->role === Role::WRITER->value) {
            return $user->id === $post->user_id;
        }

        return $user->role === Role::READER->value;
    }

    // Check authorship
    public function update(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }

    // Check authorship
    public function delete(User $user, Comment $comment): bool
    {
        return $user->id === $comment->user_id;
    }
}

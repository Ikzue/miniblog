<?php

namespace App\Policies;


use App\Models\Comment;
use App\Models\User;
use App\Enums\Role;

class CommentPolicy
{
    // Moderators can update all comments, others need to be comment author
    public function update(User $user, Comment $comment): bool
    {
        if ($user->role === Role::MODERATOR->value) {
            return true;
        }
        return $user->id === $comment->user_id;
    }

    // Moderators can delete all comments, others need to be comment author
    public function delete(User $user, Comment $comment): bool
    {
        if ($user->role === Role::MODERATOR->value) {
            return true;
        }
        return $user->id === $comment->user_id;
    }
}

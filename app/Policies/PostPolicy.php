<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Enums\Role;

class PostPolicy
{
    // Only writers and moderators can create posts
    public function create(User $user): bool
    {
        return $user->role === Role::WRITER->value || $user->role === Role::MODERATOR->value;
    }

    // Moderators can update all posts, others need to be author
    public function update(User $user, Post $post): bool
    {
        if ($user->role === Role::MODERATOR->value) {
            return true;
        }
        return $user->id === $post->user_id;
    }

    // Only moderators can delete all posts
    public function delete(User $user, Post $post): bool
    {
        if ($user->role === Role::MODERATOR->value) {
            return true;
        }
        return false;
    }

    // Moderators and readers can comment on all posts, writers need to be post author to comment
    public function canComment(User $user, Post $post): bool
    {
        if ($user->role === Role::MODERATOR->value || $user->role === Role::READER->value) {
            return true;
        }
        else if ($user->role === Role::WRITER->value) {
            return $user->id === $post->user_id;
        }
        return false;
    }
}

<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use App\Enums\Role;

class PostPolicy
{
    public function before(User $user,  string $ability)
    {
        if ($user->role === Role::MODERATOR->value) {
            return true;
        }
        return null;
    }

    // Writers can create posts
    public function create(User $user): bool
    {
        return $user->role === Role::WRITER->value;
    }

    // Check authorship
    public function update(User $user, Post $post): bool
    {
        return $user->id === $post->user_id;
    }

    // Only moderators can delete
    public function delete(User $user, Post $post): bool
    {
        return false;
    }
}

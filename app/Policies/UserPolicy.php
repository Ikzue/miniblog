<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Enums\Role;

class UserPolicy
{
    /**
     * Only moderators can CRUD blog users.
     */
    public function before(User $user,  string $ability)
    {
        if ($user->role === Role::MODERATOR->value) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user): bool
    {
        return false;
    }

    public function update(User $user): bool
    {
        return false;
    }

    public function delete(User $user): bool
    {
        return false;
    }
}

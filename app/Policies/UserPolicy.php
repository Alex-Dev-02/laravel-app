<?php

namespace App\Policies;

use App\Models\User;
Use App\Models\Post;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Определяет можно ли создавать пользователя.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user): bool
    {
        return $user->role === 'admin';
    }
}

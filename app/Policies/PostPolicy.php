<?php

namespace App\Policies;

use Illuminate\Support\Carbon;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Политика для ограничения количества запросов для просмотра постов
     *
     * @param $user
     * @return bool
     */

    public function create(User $user): bool
    {
        $postsCountPerDay = $user->posts()
            ->whereDate('created_at', Carbon::today())
            ->count();

        return $postsCountPerDay < 5;
    }

    public function update(User $user, Post $post): bool
    {
        return $user->role === 'admin' || $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->role === 'admin' || $user->id === $post->user_id;
    }
}
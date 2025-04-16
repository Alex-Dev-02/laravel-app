<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Support\Facades\Gate;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends BaseController
{
    /**
     * Получает всех пользователей и количество их постов
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersAndTheirPostsCount(): \Illuminate\Http\JsonResponse
    {
        return response()->json(User::withCount('posts')->get());
    }

    /**
     * Получает пользователя по его id
     *
     * @param $id
     * @return JsonResponse
     */
    public function getUserById(int $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(User::find($id));
    }

    /**
     * Получает список всех пользователей, которые писали посты в категории, запрошенной через параметр
     *
     * @param $categoryId
     * @return AnonymousResourceCollection
     */
    public function getUsersByCategory(int $categoryId): AnonymousResourceCollection
    {
        $users = User::whereHas('posts', function ($query) use ($categoryId) {
            $query->whereHas('categories', function($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        })->get();

        return UserResource::collection($users);
    }

/**
     * Создает нового пользователя из переданных в запросе значений
     *
     * @param $request
     * @return AnonymousResourceCollection
     */
    public function createUser(UserRequest $request): AnonymousResourceCollection
    {
        $userParameters['name'] = $request->name;
        $userParameters['email'] = $request->email;
        $userParameters['role'] = $request->role;
        $userParameters['password'] = $request->password;
        $userParameters['is_moderated'] = false;
        
        Gate::authorize('create', User::class);

        User::create($userParameters);

        return UserResource::collection(User::all());
    }

    /**
     * Изменяет пользователя по переданным в запросе значениям
     *
     * @param $request, $id
     * @return AnonymousResourceCollection
     */
    public function updateUser(UserRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        } 

        if (Gate::denies('update', $user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->update($request->only(['name', 'email', 'role']));
        
        return response()->json(new UserResource($user));
    }

    /**
     * Удаляет пользователя по его id
     *
     * @param $id
     * @return AnonymousResourceCollection
     */
    public function deleteUser(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if (Gate::denies('delete', $user)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
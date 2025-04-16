<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignInUserRequest;
use App\Http\Requests\SignUpUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthController extends Controller
{
    /**
     * Регистрирует нового пользователя для работы с API
     *
     * @param $request
     * @return JsonResponse
     */
    public function signUp(SignUpUserRequest $request): UserResource
    {
        return new UserResource(User::create($request->all()));            
    }

    /**
     * Авторизует пользователя для работы с API
     *
     * @param $request
     * @return JsonResponse
     */
    public function signIn(SignInUserRequest $request): \Illuminate\Http\JsonResponse
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Email и пароль не верны'
            ], 401);
        }

        $user = User::query()->where('email', $request->email)->first();

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $user->createToken("Токен пользователя {$user->name}")->plainTextToken
        ], 200);
    }

    /**
     * Разлогинивает пользователя и удаляет токен
     *
     * @param $request
     * @return JsonResponse
     */
    public function signOut(): \Illuminate\Http\JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Токен удален'
        ]);
    }
}
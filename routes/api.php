<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->get('/user', function (Request $request) {
    return $request->user();
});

// Запросы к api для авторизации
Route::prefix('v1')->group(function() {
    Route::post('/signup', [AuthController::class, 'signUp']);
    Route::post('/signin', [AuthController::class, 'signIn']);
});

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function() {
    // Получение иформации о пользователях
    Route::prefix('users')->group(function() {
        Route::get('/', [UserController::class, 'getUsersAndTheirPostsCount']);
        Route::get('/{id}', [UserController::class, 'getuserById']);
        Route::get('/by-category/{category_id}', [UserController::class, 'getUsersByCategory']);
        Route::post('/', [UserController::class, 'createUser']);
        Route::put('/{id}', [UserController::class, 'updateUser']);
        Route::delete('/{id}', [UserController::class, 'deleteUser']);
    });

    // Получение иформации о постах
    Route::prefix('posts')->group(function() {
        Route::get('/', [PostController::class, 'getAllPosts']);
        Route::get('/by-keyword', [PostController::class, 'getPostsByKeyWord']);
        Route::get('/{id}', [PostController::class, 'getPostById']);
        Route::post('/', [PostController::class, 'createPost']);
        Route::put('/{id}', [PostController::class, 'updatePost']);
        Route::delete('/{id}', [PostController::class, 'deletePost']);
    });

    // Получение иформации о категориях
    Route::prefix('categories')->group(function() {
        Route::get('/', [CategoryController::class, 'getTreeOfCategories']);
        Route::get('/{id}', [CategoryController::class, 'getCategoryById']);
        Route::post('/', [CategoryController::class, 'createCategory']); 
        Route::get('/with-post-count', [CategoryController::class, 'getCategoriesAndTheirPostsCount']);
        Route::post('/{categoryId}/posts/{postId}', [CategoryController::class, 'bindPostToCategory']);
        Route::put('/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('/{id}', [CategoryController::class, 'deleteCategory']);
    });

    // Завершение работы с пользователем
    Route::get('signout', [AuthController::class, 'signOut']);
});
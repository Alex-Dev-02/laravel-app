<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller as BaseController;

class PostController extends BaseController
{
    /**
     * Получает все посты с учетом параметра модерации
     *
     * @param $request
     * @return JsonResponse
     */
    public function getAllPosts(PostRequest $request): \Illuminate\Http\JsonResponse
    {
        $is_moderated = $request->query('is_moderated');

        if ($is_moderated !== null) {
            $is_moderated = filter_var($is_moderated, FILTER_VALIDATE_BOOLEAN);
            $posts = Post::where('is_moderated', $is_moderated)->get();
        } else {
            $posts = Post::all();
        }

        return response()->json($posts);
    }

    /**
     * Получает все посты, которые содержат в заголовке или тексте ключевое слово, задаваемое через параметр
     *
     * @param $request
     * @return AnonymousResourceCollection
     */
    public function getPostsByKeyWord(PostRequest $request): AnonymousResourceCollection
    {
        $keyword = $request->query('keyword');
        $posts = Post::where('title', 'like', '%' . $keyword . '%')
            ->orWhere('content', 'like', '%' . $keyword . '%')
            ->get();

        return PostResource::collection($posts);
    }

    /**
     * Получает пост по его id
     *
     * @param $id
     * @return AnonymousResourceCollection
     */
    public function getPostById(int $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(Post::find($id));
    }

    /**
     * Создает новый пост из переданных в запросе значений
     *
     * @param $request
     * @return AnonymousResourceCollection
     */
    public function createPost(PostRequest $request): AnonymousResourceCollection
    {
        $categoryParameters = [];
        
        $categoryParameters['title'] = $request->title;
        $categoryParameters['content'] = $request->content;
        $categoryParameters['user_id'] = $request->user_id;
        $categoryParameters['is_moderated'] = $request->is_moderated;
        
        Gate::authorize('create', Post::class);

        Post::create($categoryParameters);

        return PostResource::collection(Post::all());
    }

    /**
     * Изменяет пост по переданным в запросе значениям
     *
     * @param $request, $id
     * @return AnonymousResourceCollection
     */
    public function updatePost(PostRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if (Gate::denies('update', $post)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
         $post->update($request->only(['title', 'content', 'user_id', 'is_moderated']));
        
        return response()->json(new PostResource($post));
    }

    /**
     * Удаляет пост по его id
     *
     * @param $id
     * @return AnonymousResourceCollection
     */
    public function deletePost(int $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'message' => 'Post not found'
            ], 404);
        }

        if (Gate::denies('delete', $post)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully'
        ]);
    }
}
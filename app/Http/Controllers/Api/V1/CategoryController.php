<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller as BaseController;

class CategoryController extends BaseController
{
    /**
     * Получает дерево категорий
     *
     * @return AnonymousResourceCollection
     */
    public function getTreeOfCategories(): AnonymousResourceCollection
    {
        $categories = Category::with(['children'])->get();

        $parentCategories = [];

        $childCategories = [];

        foreach ($categories as $category) {
            if ($category->parent_id === 0) {
                $parentCategories[] = $category;
            } else {
                $childCategories[$category->parent_id][] = $category;
            }
        }

        foreach ($parentCategories as $parentCategory) {
            $parentId = $parentCategory->id;
            if (isset($childCategories[$parentId])) {
                $parentCategory->setRelation('children', collect($childCategories[$parentId]));
            }
        }

        return CategoryResource::collection($parentCategories);
    }

    /**
     * Получает список категорий и количество постов в каждой
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoriesAndTheirPostsCount(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Category::withCount('posts')->get());
    }

    /**
     * Получает категорию по её id
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategoryById(int $id): \Illuminate\Http\JsonResponse
    {
        return response()->json(Category::find($id));
    }

    /**
     * Создает новую категорию из переданных в запросе значений
     *
     * @param $request
     * @return AnonymousResourceCollection
     */
    public function createCategory(CategoryRequest $request): AnonymousResourceCollection
    {
        if (!empty($request->name)) {
            $categoryParameters['name'] = $request->name;
        }
        
        if (!empty($request->parent_id)) {
            $categoryParameters['parent_id'] = $request->parent_id;
        }

        Category::create($categoryParameters);

        return CategoryResource::collection(Category::all());
    }

    /**
     * Изменяет существующую категорию на данные из переданных в запросе значений
     *
     * @param $request, $id
     * @return JsonResponse
     */
    public function updateCategory(CategoryRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($id);
        $categoryParameters = [];

        if (isset($request->name)) {
            $categoryParameters['name'] = $request->name;
        }

        if (isset($request->parent_id)) {
            $categoryParameters['parent_id'] = $request->parent_id;
        }

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        } 
        
        $category->update($categoryParameters);
        
        return response()->json(new CategoryResource($category));
    }

    /**
     * Связывает пост с определенной категорией
     *
     * @param $request
     * @return JsonResponse
     */
    public function bindPostToCategory(CategoryRequest $request): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($request->categoryId);

        if (!$category) {
            return response()->json(['message' => 'Категория не найдена'], 404);
        }

        $post = Post::find($request->postId);

        if (!$post) {
            return response()->json(['message' => 'Пост не найден'], 404);
        }

        if ($post->categories()->where('id', $category->id)->exists()) {
            return response()->json(['message' => 'Пост уже связан с категорией'], 400);
        }

        $post->categories()->attach($category);

        return response()->json(['message' => 'Пост ' . $post->title . ' связан с категорией ' . $category->name], 200);
    }

    /**
     * Удаляет категорию по её id
     *
     * @param $id
     * @return JsonResponse
     */
    public function deleteCategory(int $id): \Illuminate\Http\JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
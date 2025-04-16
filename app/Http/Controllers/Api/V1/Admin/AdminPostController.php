<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminPostController extends Controller
{
    /**
     * Выполняет модерацию
     * (пока нигде не используется, в задаче было только про флаг модерации, это сделал как заготовку на будущее)
     * 
     * @param $postId
     * @return JsonResponse
     */
    public function moderate($postId): \Illuminate\Http\JsonResponse
    {
        $post = Post::findOrFail($postId);
        $post->is_moderated = true;
        $post->save();

        return response()->json(['message' => 'Post has been moderated'], 200);
    }
}

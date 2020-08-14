<?php

namespace App\Http\Controllers\Apis;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class ArticleApi extends Controller
{
    public function index()
    {
        $videos = Article::where('status', 1)
            ->where('type', Article::TYPE_VIDEO)
            ->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->take(3)
            ->get();
        $reads = Article::where('status', 1)
            ->where('type', Article::TYPE_READ)
            ->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->take(3)
            ->get();
        return response()->json([
            'reads' => $reads,
            'videos' => $videos,
        ]);
    }

    public function loadByType(Request $request, $type)
    {
        $data = Article::where('status', 1)
            ->where('type', $type)
            ->orderby('is_hot', 'desc')
            ->orderby('id', 'desc')
            ->paginate(12);

        return response()->json($data);
    }

    public function loadArticle($id)
    {
        $data = Article::find($id);
        if (!$data) {
            return response('Bài viết không có', 404);
        }
        Log::debug($data);
        return response()->json($data);
    }
}

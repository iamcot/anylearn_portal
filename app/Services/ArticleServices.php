<?php

namespace App\Services;

use App\Constants\OrderConstants;
use App\Models\Article;
use App\Models\VoucherEvent;
use Illuminate\Support\Facades\DB;


class ArticleServices
{
    public function getArticles() 
    {
        return Article::where('status', 1)
            ->whereIn('type', [Article::TYPE_READ, Article::TYPE_VIDEO])
            ->orderByDesc('id')
            ->take(5)
            ->get()
            ->makeHidden(['content']);
    }

    public function getPromotions()
    {
        return Article::where('type', Article::TYPE_PROMOTION)
            ->where('status', 1)
            ->orderByDesc('id')
            ->take(5)
            ->get();
    }
}

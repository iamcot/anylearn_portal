<?php

namespace App\Http\Controllers;

use App\Constants\FileConstants;
use App\Models\Article;
use App\Models\Tag;
use App\Services\FileServices;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function list(Request $request)
    {
        $this->data['list'] = Article::paginate(20);
        return view('article.list', $this->data);
    }

    public function create(Request $request)
    {
        if ($request->get('save')) {
            $input = $request->all();
            $fileService = new FileServices();
            $file = $fileService->doUploadImage($request, 'image', FileConstants::DISK_S3, true, 'articles');
            if ($file !== false) {
                $input['image'] = $file['url'];
            }
            $input['user_id'] = Auth::user()->id;
            $newArticle = Article::create($input);
            $tagsModel = new Tag();
            $tagsModel->createTagFromItem($newArticle, Tag::TYPE_ARTICLE);
            return redirect()->route('article')->with('notify', $newArticle ? 1 : 0);
        }
        return view('article.form', $this->data);
    }

    public function edit(Request $request, $id)
    {
        if ($request->get('save')) {
            $input = $request->all();
            $fileService = new FileServices();
            $file = $fileService->doUploadImage($request, 'image', FileConstants::DISK_S3, true, 'articles');
            if ($file !== false) {
                $input['image'] = $file['url'];
            }
            $newArticle = Article::find($id)->update($input);
            $tagsModel = new Tag();
            $tagsModel->createTagFromItem(Article::find($id), Tag::TYPE_ARTICLE);
            return redirect()->route('article')->with('notify', $newArticle ? 1 : 0);
        }
        $this->data['article'] = Article::find($id);
        return view('article.form', $this->data);
    }

    public function statusTouch($userId)
    {
        $rs = Article::find($userId)->update(['status' => DB::raw('1 - status')]);
        return redirect()->back()->with('notify', $rs);
    }
}

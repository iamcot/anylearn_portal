<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $input['user_id'] = Auth::user()->id;
            $newArticle = Article::create($input);
            return redirect()->route('article')->with('notify', $newArticle ? 1 : 0);
        }
        return view('article.form', $this->data);
    }

    public function edit(Request $request, $id)
    {
        if ($request->get('save')) {
            $input = $request->all();
            $newArticle = Article::find($id)->update($input);
            return redirect()->route('article')->with('notify', $newArticle ? 1 : 0);
        }
        $this->data['article'] = Article::find($id);
        return view('article.form', $this->data);
    }
}

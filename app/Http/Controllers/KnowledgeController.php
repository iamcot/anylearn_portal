<?php

namespace App\Http\Controllers;

use App\Constants\FileConstants;
use App\Models\Article;
use App\Models\I18nContent;
use App\Models\Knowledge;
use App\Models\KnowledgeCategory;
use App\Models\KnowledgeTopic;
use App\Models\KnowledgeTopicCategoryLink;
use App\Models\Tag;
use App\Services\FileServices;
use App\Services\UserServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KnowledgeController extends Controller
{
    public function category(Request $request)
    {
        $data = KnowledgeCategory::paginate();
        $i18nModel = new I18nContent();

        // change vi->en
        foreach ($data as $row) {
            foreach (I18nContent::$supports as $locale) {
                if ($locale == I18nContent::DEFAULT) {
                    foreach (I18nContent::$knowledge_categoriesCols as $col => $type) {
                        $row->$col =  [I18nContent::DEFAULT => $row->$col];
                    }
                } else {
                    $item18nData = $i18nModel->i18nknowledgeCategory($row->id, $locale);
                    $supportCols = array_keys(I18nContent::$knowledge_categoriesCols);

                    foreach ($supportCols as $col) {
                        if (empty($item18nData[$col])) {
                            $row->$col = $row->$col + [$locale => ""];
                        } else {
                            $row->$col = $row->$col + [$locale => $item18nData[$col]];
                        }
                    }
                }
            }
        }
        $this->data['categories'] = $data;
        return view('knowledge.category.index', $this->data);
    }

    public function categoryEdit(Request $request, $id = null)
    {
        if ($request->get('save')) {
            foreach (I18nContent::$supports as $locale) {
                $input = $request->all();
                // dd($input);
                $category = $input["title"];
                $url = Str::slug($category[$locale]);
                $catId = $request->get('id');
                $data = [
                    'title' => $category[$locale],
                    'url' => $url,
                ];
                $i18n = new I18nContent();
                if ($catId) {
                    if ($locale != I18nContent::DEFAULT) {
                        $i18n->i18nSave($locale, 'knowledge_categories', $catId, 'title', $category[$locale]);
                    } else {
                        KnowledgeCategory::find($catId)->update($data);
                    }
                } else {
                    if ($locale == I18nContent::DEFAULT) {
                        $id = KnowledgeCategory::create($data)->id;
                    } else {
                        $i18n->i18nSave($locale, 'knowledge_categories', $id, 'title', $category[$locale]);
                    }
                }
            }
            return redirect()->route('knowledge.category')->with('notify', 'Thành công');
        }
        if ($id) {
            $data = KnowledgeCategory::find($id);
            $i18nModel = new I18nContent();

            // change vi->en

            foreach (I18nContent::$supports as $locale) {
                if ($locale == I18nContent::DEFAULT) {
                    foreach (I18nContent::$knowledge_categoriesCols as $col => $type) {
                        $data->$col = [I18nContent::DEFAULT => $data->$col];
                    }
                } else {
                    $supportCols = array_keys(I18nContent::$knowledge_categoriesCols);
                    $item18nData = $i18nModel->i18nknowledgeCategory($data->id, $locale);
                    foreach ($supportCols as $col) {
                        if (empty($item18nData[$col])) {
                            $data->$col = $data->$col + [$locale => ""];
                        } else {
                            $data->$col = $data->$col + [$locale => $item18nData[$col]];
                        }
                    }
                }
            }
            $this->data['category'] = $data;
        }
        return view('knowledge.category.form', $this->data);
    }

    public function knowledge(Request $request)
    {
        $data = Knowledge::paginate();
        $i18nModel = new I18nContent();

        // change vi->en
        foreach ($data as $row) {
            foreach (I18nContent::$supports as $locale) {
                if ($locale == I18nContent::DEFAULT) {
                    foreach (I18nContent::$knowledgeCols as $col => $type) {
                        $row->$col =  [I18nContent::DEFAULT => $row->$col];
                    }
                } else {
                    $item18nData = $i18nModel->i18nKnowledge($row->id, $locale);
                    $supportCols = array_keys(I18nContent::$knowledgeCols);

                    foreach ($supportCols as $col) {
                        if (empty($item18nData[$col])) {
                            $row->$col = $row->$col + [$locale => ""];
                        } else {
                            $row->$col = $row->$col + [$locale => $item18nData[$col]];
                        }
                    }
                }
            }
        }
        $this->data['articles'] = $data;
        return view('knowledge.article.index', $this->data);
    }

    public function knowledgeEdit(Request $request, $id = null)
    {
        if ($request->get('save')) {
            foreach (I18nContent::$supports as $locale) {
                $input = $request->all();
                //  dd($input);
                $title = $input["title"];
                $url = Str::slug($title[$locale]);
                $articleId = $request->get('id');
                $catId = $request->get('knowledge_category_id');
                $content = $input["content"];
                $contentBot = $input["content_bot"];
                $data = [
                    'title' => $title[$locale],
                    'url' => $url,
                    'knowledge_category_id' => $catId,
                    'content' => $content[$locale],
                    'content_bot' => $contentBot[$locale],
                ];
                $i18n = new I18nContent();
                if ($articleId) {
                    if ($locale != I18nContent::DEFAULT) {
                        $i18n->i18nSave($locale, 'knowledges', $articleId, 'title', $title[$locale]);
                        $i18n->i18nSave($locale, 'knowledges', $articleId, 'content', $content[$locale]);
                        $i18n->i18nSave($locale, 'knowledges', $articleId, 'content_bot', $contentBot[$locale]);
                    } else {
                        Knowledge::find($catId)->update($data);
                    }
                } else {
                    if ($locale == I18nContent::DEFAULT) {
                        $id = Knowledge::create($data)->id;
                    } else {
                        $i18n->i18nSave($locale, 'knowledges', $articleId, 'title', $title[$locale]);
                        $i18n->i18nSave($locale, 'knowledges', $articleId, 'content', $content[$locale]);
                        $i18n->i18nSave($locale, 'knowledges', $articleId, 'content_bot', $contentBot[$locale]);
                    };
                }
            }

            return redirect()->route('knowledge')->with('notify', 'Thành công');
        }
        if ($id) {
            $data= Knowledge::find($id);
            $i18nModel = new I18nContent();

            // change vi->en

            foreach (I18nContent::$supports as $locale) {
                if ($locale == I18nContent::DEFAULT) {
                    foreach (I18nContent::$knowledgeCols as $col => $type) {
                        $data->$col = [I18nContent::DEFAULT => $data->$col];
                    }
                } else {
                    $supportCols = array_keys(I18nContent::$knowledgeCols);
                    $item18nData = $i18nModel->i18nKnowledge($data->id, $locale);
                    foreach ($supportCols as $col) {
                        if (empty($item18nData[$col])) {
                            $data->$col = $data->$col + [$locale => ""];
                        } else {
                            $data->$col = $data->$col + [$locale => $item18nData[$col]];
                        }
                    }
                }
            }
            $this->data['article'] = $data;
        }
        $this->data['categories'] = KnowledgeCategory::all();
        return view('knowledge.article.form', $this->data);
    }

    public function topic(Request $request)
    {
        $data = KnowledgeTopic::paginate();
        $i18nModel = new I18nContent();

        // change vi->en
        foreach ($data as $row) {
            foreach (I18nContent::$supports as $locale) {
                if ($locale == I18nContent::DEFAULT) {
                    foreach (I18nContent::$knowledgetopicCols as $col => $type) {
                        $row->$col =  [I18nContent::DEFAULT => $row->$col];
                    }
                } else {
                    $item18nData = $i18nModel->i18nTopic($row->id, $locale);
                    $supportCols = array_keys(I18nContent::$knowledgetopicCols);

                    foreach ($supportCols as $col) {
                        if (empty($item18nData[$col])) {
                            $row->$col = $row->$col + [$locale => ""];
                        } else {
                            $row->$col = $row->$col + [$locale => $item18nData[$col]];
                        }
                    }
                }
            }
        }
        // dd($data);
        $this->data['categories'] = $data;
        return view('knowledge.topic.index', $this->data);
    }

    public function topicEdit(Request $request, $id = null)
    {
        if ($request->get('save')) {
            foreach (I18nContent::$supports as $locale) {
                $input = $request->all();
                // dd($input);
                $category = $input["title"];
                $url = Str::slug($category[$locale]);
                $catId = $request->get('id');
                $data = [
                    'title' => $category[$locale],
                    'url' => $url,
                ];
                $i18n = new I18nContent();
                if ($catId) {
                    if ($locale != I18nContent::DEFAULT) {
                        $i18n->i18nSave($locale, 'knowledge_topics', $catId, 'title', $category[$locale]);
                    } else {
                        KnowledgeTopic::find($catId)->update($data);
                    }
                } else {
                    if ($locale == I18nContent::DEFAULT) {
                        $id = KnowledgeTopic::create($data)->id;
                    } else {
                        $i18n->i18nSave($locale, 'knowledge_topics', $id, 'title', $category[$locale]);
                    }
                }
            }
            return redirect()->route('knowledge.topic')->with('notify', 'Thành công');
        }
        if ($id) {
            $data = KnowledgeTopic::find($id);
            $i18nModel = new I18nContent();

            // change vi->en

            foreach (I18nContent::$supports as $locale) {
                if ($locale == I18nContent::DEFAULT) {
                    foreach (I18nContent::$knowledgetopicCols as $col => $type) {
                        $data->$col = [I18nContent::DEFAULT => $data->$col];
                    }
                } else {
                    $supportCols = array_keys(I18nContent::$knowledgetopicCols);
                    $item18nData = $i18nModel->i18nTopic($data->id, $locale);
                    foreach ($supportCols as $col) {
                        if (empty($item18nData[$col])) {
                            $data->$col = $data->$col + [$locale => ""];
                        } else {
                            $data->$col = $data->$col + [$locale => $item18nData[$col]];
                        }
                    }
                }
            }
            $this->data['category'] = $data;
        }
        return view('knowledge.topic.form', $this->data);
    }

    public function topicCategory(Request $request, $topicId) {
        if ($request->get('save')) {
            $categories = $request->get('categories');
            KnowledgeTopicCategoryLink::where('knowledge_topic_id', $topicId)->delete();
            if ($categories) {
                $catIds = array_keys($categories);
                foreach($catIds as $id) {
                    KnowledgeTopicCategoryLink::create([
                        'knowledge_topic_id' => $topicId,
                        'knowledge_category_id' => $id
                    ]);
                }
            }
            return redirect()->route('knowledge.topic.category', ['id' => $topicId])->with('notify', 'Thành công');
        }
        $this->data['topic'] = KnowledgeTopic::find($topicId);
        $this->data['categories'] = KnowledgeCategory::all();
        $links = KnowledgeTopicCategoryLink::where('knowledge_topic_id', $topicId)->get();
        $this->data['topicCategories'] = [];
        if ($links) {
            foreach($links as $link) {
                $this->data['topicCategories'][] = $link->knowledge_category_id;
            }
        }
        $this->data['hasBack'] = route('knowledge.topic');
        return view('knowledge.topic.category', $this->data);
    }

    public function statusTouch($userId)
    {
        $rs = Article::find($userId)->update(['status' => DB::raw('1 - status')]);
        return redirect()->back()->with('notify', $rs);
    }
}

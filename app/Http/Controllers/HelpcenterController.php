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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HelpcenterController extends Controller
{
    public function index(Request $request)
    {
        $data = Knowledge::orderBy('is_top_question', 'desc')
        ->where('status', '>', 0)
        ->orderby('view', 'desc')
        ->take(10)->get();
        $locale = App::getLocale();
            if($locale!=I18nContent::DEFAULT){
                $i18 = new I18nContent();
                foreach ($data as $row) {
                    // dd($row);
                    $item18nData = $i18->i18nKnowledge($row->id, $locale);
                    $supportCols = array_keys(I18nContent::$knowledgeCols);
                    foreach ($item18nData as $col => $content) {
                        if (in_array($col, $supportCols) && $content != "") {
                            $row->$col = $content;
                        }
                    }
                }
            }
        $this->data['topKnowledge'] = $data;
        $topics = KnowledgeTopic::where('status', '>', 0)->get();
        if($locale!=I18nContent::DEFAULT){
            $i18 = new I18nContent();
            foreach ($topics as $row) {
                // dd($row);
                $item18nData = $i18->i18nTopic($row->id, $locale);
                $supportCols = array_keys(I18nContent::$knowledgetopicCols);
                foreach ($item18nData as $col => $content) {
                    if (in_array($col, $supportCols) && $content != "") {
                        $row->$col = $content;
                    }
                }
            }
        }
        $this->data['topics'] = $topics;
        $this->data['breadcrumb'] = [
            [
                'text' => 'Trung tâm hỗ trợ',
            ]
        ];
        return view(env('TEMPLATE', '') . 'helpcenter.index', $this->data);
    }

    public function topic(Request $request, $topicUrl)
    {
        $topic = KnowledgeTopic::where('url', $topicUrl)->where('status', '>', 0)->first();
        $locale = App::getLocale();

        if (!$topic) {
            return redirect()->route('helpcenter');
        } else{
            if($locale!=I18nContent::DEFAULT){
                $i18 = new I18nContent();
                    $item18nData = $i18->i18nTopic($topic->id, $locale);
                    $supportCols = array_keys(I18nContent::$knowledgeCols);
                    foreach ($item18nData as $col => $content) {
                        if (in_array($col, $supportCols) && $content != "") {
                            $topic->$col = $content;
                        }
                    }
            }
        }

        $this->data['topic'] = $topic;
        $catsInTopic = DB::table('knowledge_topic_category_links')
        ->where('knowledge_topic_id', $topic->id)
        ->where('status',1)
        ->join('knowledge_categories', 'knowledge_categories.id', '=', 'knowledge_topic_category_links.knowledge_category_id')
        ->select('title', 'knowledge_categories.id')
        ->get();
        if($locale!=I18nContent::DEFAULT){
            $i18 = new I18nContent();
            foreach ($catsInTopic as $row) {
                // dd($row);
                $item18nData = $i18->i18nknowledgeCategory($row->id, $locale);
                $supportCols = array_keys(I18nContent::$knowledge_categoriesCols);
                foreach ($item18nData as $col => $content) {
                    if (in_array($col, $supportCols) && $content != "") {
                        $row->$col = $content;
                    }
                }
            }
        }
        // dd($catsInTopic);
        $catwithKnowledge = [];
        foreach($catsInTopic as $cat) {
            $knowledges = Knowledge::where('knowledge_category_id', $cat->id)->where('status', '>', 0)
            ->get();
            if($locale!=I18nContent::DEFAULT){
                $i18 = new I18nContent();
                foreach ($knowledges as $row) {
                    // dd($row);
                    $item18nData = $i18->i18nKnowledge($row->id, $locale);
                    $supportCols = array_keys(I18nContent::$knowledgeCols);
                    foreach ($item18nData as $col => $content) {
                        if (in_array($col, $supportCols) && $content != "") {
                            $row->$col = $content;
                        }
                    }
                }
            }

            if ($knowledges) {
                $catwithKnowledge[$cat->id] = [
                    'cat' => $cat->title,
                    'knowledges' => $knowledges,
                ];
            }
        }
        $this->data['breadcrumb'] = [
            [
                'url' => '/helpcenter',
                'text' => 'Trung tâm hỗ trợ',
            ],
            [
                'text' => $topic->title,
            ]
        ];
        $this->data['catWithKnowledge'] = $catwithKnowledge;
        $topics = KnowledgeTopic::where('id', '!=', $topic->id)->where('status', '>', 0)->get();
        if($locale!=I18nContent::DEFAULT){
            $i18 = new I18nContent();
            foreach ($topics as $row) {
                // dd($row);
                $item18nData = $i18->i18nTopic($row->id, $locale);
                $supportCols = array_keys(I18nContent::$knowledgetopicCols);
                foreach ($item18nData as $col => $content) {
                    if (in_array($col, $supportCols) && $content != "") {
                        $row->$col = $content;
                    }
                }
            }
        }
        $this->data['topics'] = $topics;
        return view(env('TEMPLATE', '') . 'helpcenter.topic', $this->data);
    }

    public function knowledge(Request $request, $id, $url)
    {
        $locale = App::getLocale();
        $knowledge = Knowledge::find($id);
        if($locale!=I18nContent::DEFAULT){
            $i18 = new I18nContent();
                $item18nData = $i18->i18nKnowledge($knowledge->id, $locale);
                $supportCols = array_keys(I18nContent::$knowledgeCols);
                foreach ($item18nData as $col => $content) {
                    if (in_array($col, $supportCols) && $content != "") {
                        $knowledge->$col = $content;
                    }
                }
        }
        $this->data['knowledge'] =$knowledge;
        $topic = DB::table('knowledge_topics')
        ->join('knowledge_topic_category_links', 'knowledge_topic_category_links.knowledge_topic_id', '=', 'knowledge_topics.id')
        ->where('knowledge_topic_category_links.knowledge_category_id', $this->data['knowledge']->knowledge_category_id)
        ->select('knowledge_topics.*')
        ->first();
        if($locale!=I18nContent::DEFAULT){
            $i18 = new I18nContent();
                $item18nData = $i18->i18nTopic($topic->id, $locale);
                // dd($item18nData);
                $supportCols = array_keys(I18nContent::$knowledgetopicCols);
                foreach ($item18nData as $col => $content) {
                    if (in_array($col, $supportCols) && $content != "") {
                        $topic->$col = $content;
                    }
                }
        }
        $data = Knowledge::where('id', '!=', $id)->where('status', '>', 0)
        ->where('knowledge_category_id', $this->data['knowledge']->knowledge_category_id)->get();
        if($locale!=I18nContent::DEFAULT){
            $i18 = new I18nContent();
            foreach ($data as $row) {
                // dd($row);
                $item18nData = $i18->i18nKnowledge($row->id, $locale);
                $supportCols = array_keys(I18nContent::$knowledgeCols);
                foreach ($item18nData as $col => $content) {
                    if (in_array($col, $supportCols) && $content != "") {
                        $row->$col = $content;
                    }
                }
            }
        }
        $this->data['others'] = $data;
        $this->data['breadcrumb'] = [
            [
                'url' => '/helpcenter',
                'text' => 'Trung tâm hỗ trợ',
            ],
            [
                'url' => route('helpcenter.topic', ['url' => $topic->url]),
                'text' => $topic->title,
            ],
            [
                'text' => $this->data['knowledge']->title,
            ]
        ];
        return view(env('TEMPLATE', '') . 'helpcenter.knowledge', $this->data);
    }

    public function chatbot()
    {
        $this->data['greetingCard'] = view(env('TEMPLATE', '') . 'helpcenter.chatbot.greetingcard')->render();
        return view(env('TEMPLATE', '') . 'helpcenter.chatbot', $this->data);
    }
}

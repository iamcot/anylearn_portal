<?php

namespace App\Services;

use App\Constants\UserConstants;
use App\Models\Item;
use App\Models\ItemVideoChapter;
use App\Models\ItemVideoLesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VideoServices{
    public function createChapter(Request $request, $input)
    {
        $result = ItemVideoChapter::create([
            'item_id'=>$input['id'],
            'chapter_no'=>$input['chapterno'],
            'title'=>$input['chaptitle'],
            'description'=>$input['chapdes']
        ]);
        return $result;
    }
    public function createLesson(Request $request, $input)
    {
        $result = ItemVideoLesson::create([
            'item_id'=>$input['id'],
            'item_video_chapter_id' => $input['idchaplklesson'],
            'lesson_no'=> $input['lessonno'],
            'title' => $input['lessonname'],
            'description'=>$input['lessondes'],
            'is_free' => $input['is_free'],
            'type' => $input['typelesson'],
            'type_value' => isset($input['youtube']) ? $input['youtube'] : (isset($input['file']) ? $input['file']: $input['stream']),
        ]);
        return $result;
    }
    public function LessoninChapter($id)
    {
        $les = DB::table('item_video_lessons')->where('item_video_chapter_id',$id)->get();
        // dd($les);
        return $les;
    }
    public function lessonItem($id)
    {
        $les = DB::table('item_video_lessons')->where('item_id',$id)->get();
        return $les;
    }
    public function getOneLessonItem($lessson_id)
    {
        $lesson = ItemVideoLesson::find($lessson_id);
        return $lesson;
    }
    public function getlinkYT($link)
    {
        $links = substr($link,strlen('https://www.youtube.com/watch?v='),strlen($link));
        return $links;
    }
    public function getTeacher($itemid)
    {
        $item = Item::Where('id',$itemid)->first();
        $teacher = User::find($item->user_id);
        return $teacher;
    }
}

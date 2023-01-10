<?php

namespace App\Services;

use App\Models\ItemVideoChapter;
use App\Models\ItemVideoLesson;
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
}

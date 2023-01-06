<?php

namespace App\Services;

use App\Models\ItemVideoChapter;
use Illuminate\Http\Request;

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
        dd($input);
        return true;
    }
}

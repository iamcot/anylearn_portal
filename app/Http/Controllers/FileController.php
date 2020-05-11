<?php

namespace App\Http\Controllers;

use App\Services\FileServices;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function ckEditorImage(Request $request) 
    {
        $obj  = [
            'data' => [],
            'success' => false
        ];
        $fileService = new FileServices();
        $file = $fileService->doUploadImage($request);
        if ($file !== false) {
            $obj = [
                'data' => [ 'url' => $file['url']],
                'success' => true
            ];
            $fileService->editorHasNewImage($file['file']);
        } 
        return response()->json($obj);
    }    
    
}

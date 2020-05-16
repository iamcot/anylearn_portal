<?php

namespace App\Services;

use App\Constants\FileConstants;
use DOMDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FileServices
{
    public static $allowFileExts = ['jpg', 'png', 'doc', 'docx', 'pdf', 'ppt', 'pptx', 'xls', 'xlsx'];

    private $systemFiles = [
        '.gitignore',
    ];

    public function doUploadFile($request, $field = 'file', $disk = 'files', $changeName = true, $childPath = '')
    {
        $rs = false;
        try {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                $extension = $request->file($field)->extension();
                if ($changeName) {
                    if (!in_array($extension, self::$allowFileExts)) {
                        Log::debug("$extension is not allowed");
                        return false;
                    }
                    $file =  time() . '.' . $extension;
                } else {
                    $file = $request->file($field)->getClientOriginalName();
                }
                $path = Storage::disk($disk)->putFileAs($childPath, $request->file($field), $file);
                if ($path) {
                    $rs = [
                        'file' => $file,
                        'path' => $path,
                        'url' => Storage::disk($disk)->url($childPath . '/' . $file),
                        'file_ext' => $extension,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
        return $rs;
    }

    public function doUploadImage($request, $field = 'file', $disk = 'images', $changeName = true, $childPath = '')
    {
        $rs = false;
        try {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                if ($changeName) {
                    $file =  time() . '.jpg';
                } else {
                    $file = $request->file($field)->getClientOriginalName();
                }
                $path = Storage::disk($disk)->putFileAs($childPath, $request->file($field), $file);
                if ($path) {
                    $rs = [
                        'file' => $file,
                        'path' => $path,
                        'url' => Storage::disk($disk)->url($childPath . '/' . $file)
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
        return $rs;
    }

    public function urlFromPath($disk, $path)
    {
        if ($path) {
            return Storage::disk($disk)->url($path);
        }
        return '';
    }

    public function removeSystemFiles($files)
    {
        foreach ($this->systemFiles as $file) {
            if (($key = array_search($file, $files)) !== false) {
                unset($files[$key]);
            }
        }
        return $files;
    }

    public function getAllFiles($disk, $folder = '')
    {
        try {
            return Storage::disk($disk)->files();
        } catch (\Exception $e) {
            Log::error($e);
        }
        return null;
    }

    public function editorHasNewImage($file)
    {
        $uploadedImages = session(FileConstants::SESSION_EDITOR_IMAGE, []);
        $uploadedImages[] = $file;
        session([FileConstants::SESSION_EDITOR_IMAGE => $uploadedImages]);
    }

    public function getImagesOfData($data)
    {
        session([FileConstants::SESSION_EDITOR_IMAGE => []]);
        if (empty($data)) {
            return;
        }
        $images = [];
        try {
            $htmlDom = new DOMDocument();
            @$htmlDom->loadHTML($this->buildFullHtml($data));
            $imageTags = $htmlDom->getElementsByTagName('img');
            foreach ($imageTags as $imageTag) {
                $imgSrc = $imageTag->getAttribute('src');
                $images[] = basename($imgSrc);
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
        session([FileConstants::SESSION_EDITOR_IMAGE => $images]);
    }

    public function cleanNotUsedImages($data)
    {
        $uploadedImages = session(FileConstants::SESSION_EDITOR_IMAGE, []);
        if (empty($uploadedImages)) {
            return true;
        }
        try {
            $htmlDom = new DOMDocument();
            @$htmlDom->loadHTML($this->buildFullHtml($data));

            $imageTags = $htmlDom->getElementsByTagName('img');
            foreach ($imageTags as $imageTag) {
                $imgSrc = $imageTag->getAttribute('src');
                $imgFile = basename($imgSrc);
                if (($key = array_search($imgFile, $uploadedImages)) !== false) {
                    unset($uploadedImages[$key]);
                }
            }
            $this->deleteFiles($uploadedImages);
            $this->cleanLastEditorImage();
        } catch (\Exception $e) {
            Log::error($e);
        }
        return false;
    }

    public function cleanLastEditorImage()
    {
        session([FileConstants::SESSION_EDITOR_IMAGE => []]);
    }

    public function encodeFileName($file)
    {
        return str_replace('.', '=', $file);
    }

    public function decodeFileName($file)
    {
        return str_replace("=", ".", $file);
    }

    public function deleteFiles($files, $disk = 'images')
    {
        foreach ($files as $file) {
            try {
                if (Storage::disk($disk)->exists($file)) {
                    Storage::disk($disk)->delete($file);
                }
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
    }

    public function fileIcon($fileExt)
    {
        $icon = '';
        switch ($fileExt) {
            case 'jpg':
            case 'png':
                $icon = '<i class="fas fa-file-image"></i>';
                break;
            case 'ppt':
            case 'pptx':
                $icon = '<i class="fas fa-file-powerpoint"></i>';
                break;
            case 'doc':
            case 'docx':
                $icon = '<i class="fas fa-file-word"></i>';
                break;
            case 'xls':
            case 'xlsx':
                $icon = '<i class="fas fa-file-excel"></i>';
                break;
            case 'pdf':
                $icon = '<i class="fas fa-file-pdf"></i>';
                break;
            default:
                $icon = '<i class="fas fa-file"></i>';
                break;
        }
        return $icon;
    }

    private function buildFullHtml($data)
    {
        return '<!DOCTYPE html><html><body>' . $data  . '</body></html>';
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function list(Request $request)
    {
        $this->data['navText'] = __('Quản lý khóa học');
        return view('course.list', $this->data);
    }

    public function create(Request $request)
    {
        $this->data['navText'] = __('Tạo khóa khóa học');
        $this->data['hasBack'] = true;
        return view('course.form', $this->data);
    }

    public function edit($courseId)
    {
        $this->data['navText'] = __('Chỉnh sửa khóa học');
        $this->data['hasBack'] = true;
        return view('course.form', $this->data);
    }

    public function detail($courseId)
    {
        $this->data['navText'] = 'Tên khóa học';
        $this->data['hasBack'] = true;
        return view('course.detail', $this->data);
    }
}

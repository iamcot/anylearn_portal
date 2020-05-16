@inject('userServ','App\Services\UserServices')
@extends('layout')

@section('rightFixedTop')
<form class="row">
    <div class="col-xs-3 mr-1">
        <select class="form-control" name="t" id="">
            <option {{ app('request')->input('t') == 'title' ? 'selected' : '' }} value="title">Tên khóa học</option>
            <option {{ app('request')->input('t') == 'series' ? 'selected' : '' }} value="series">Chuỗi khóa học</option>
        </select>
    </div>
    <div class="col-xs-6 mr-1">
        <input value="{{ app('request')->input('s') }}" type="text" class="form-control" name="s" placeholder="{{ __('Tìm kiếm') }}" />
    </div>
    <div class="col-xs-1 mr-1">
        <button class="btn btn-primary btn"><i class="fas fa-search"></i></button>
    </div>

    <div class="col-xs-2 mr-1">
        <a class="btn btn-success" href="{{ route('course.create') }}"><i class="fas fa-plus"></i> @lang('Thêm mới')</a>
    </div>
</form>
@endsection

@section('body')
@if(sizeof($courseList) == 0)
<div class="text-center mt-5 pt-5">
    @lang('Chưa có khóa học nào. <a href=":url"><i class="fas fa-plus"></i> Thêm mới</a>', ['url' => route('course.create')])
</div>
@else
<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-bordered table-striped">
            <thead>
                <thead>
                    <th class="text-center">#</th>
                    @if($userServ->isMod()) <th width="5%" class="text-center">Hot</th>@endif
                    <th>Khóa học</th>
                    <th>Chuỗi</th>
                    <th>Thời gian</th>
                    <th>Học phí</th>
                    <th>Quan tâm</th>
                    <th>Đăng ký</th>
                    <th>Lần sửa cuối</th>
                </thead>
            <tbody>
                @foreach($courseList as $course)
                <tr>
                    <th class="text-center">{{ $loop->index + 1 }}</th>
                    @if($userServ->isMod()) <td class="text-center"><a href="{{ route('ajax.touch.ishot', ['table' => 'items', 'id' =>  $course->id ]) }}">{!! $userServ->hotIcon($course->is_hot) !!}</a></td>@endif
                    <td><a href="{{ route('course.edit', ['id' => $course->id]) }}"><i class="fas fa-edit"></i> {{ $course->title }}</a></td>
                    <td>@if($course->series)
                        <a href="?t=series&s={{ $course->series->id }}">{{ $course->series->title }}</a>
                        @endif</td>
                    <td>{{ $course->time_start }}-{{ $course->time_end }} {{ date('d/m/y', strtotime($course->date_start))}}</td>
                    <td>{{ number_format($course->price) }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ $course->updated_at }}</td>
                </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
    </div>
    <div class="card-footer">
        {{ $courseList->links() }}
    </div>
</div>

@endif

@endsection
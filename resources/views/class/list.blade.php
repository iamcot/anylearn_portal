@inject('userServ','App\Services\UserServices')
@inject('itemServ','App\Services\ItemServices')
@extends('layout')

@section('rightFixedTop')
<form class="row">
    <div class="col-xs-3 mr-1">
        <select class="form-control" name="t" id="">
            <option {{ app('request')->input('t') == 'title' ? 'selected' : '' }} value="title">Tên lớp học</option>
        </select>
    </div>
    <div class="col-xs-6 mr-1">
        <input value="{{ app('request')->input('s') }}" type="text" class="form-control" name="s" placeholder="{{ __('Tìm kiếm') }}" />
    </div>
    <div class="col-xs-1 mr-1">
        <button class="btn btn-primary btn"><i class="fas fa-search"></i></button>
    </div>

    <div class="col-xs-2 mr-1">
        <a class="btn btn-success" href="{{ route('class.create') }}"><i class="fas fa-plus"></i> @lang('Thêm mới')</a>
    </div>
</form>
@endsection

@section('body')
@if(sizeof($courseList) == 0)
<div class="text-center mt-5 pt-5">
    @lang('Chưa có lớp học nào. <a href=":url"><i class="fas fa-plus"></i> Thêm mới</a>', ['url' => route('class.create')])
</div>
@else
<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-bordered table-striped">
            <thead>
                <thead>
                    <th class="text-center">#</th>
                    @if($userServ->isMod()) <th width="5%" class="text-center">Hot</th>@endif
                    @if($userServ->isMod()) <th width="5%" class="text-center">Trường</th>@endif
                    <th>Khóa học</th>
                    <th>Thời gian</th>
                    <th>Học phí</th>
                    <!-- <th>Quan tâm</th> -->
                    <th>Đăng ký</th>
                    <th>Lần sửa cuối</th>
                    <th>Thao tác</th>
                </thead>
            <tbody>
                @foreach($courseList as $course)
                <tr>
                    <th class="text-center">{{ $course->id }}</th>
                    @if($userServ->isMod()) <td class="text-center"><a href="{{ route('ajax.touch.ishot', ['table' => 'items', 'id' =>  $course->id ]) }}">{!! $userServ->hotIcon($course->is_hot) !!}</a></td>@endif
                    @if($userServ->isMod()) <td class="text-center">
                        {{ $course->user->name }}
                    </td>@endif
                    <td><a href="{{ route('class.edit', ['id' => $course->id]) }}"><i class="fas fa-edit"></i> {{ $course->title }}</a></td>
                    <td>{{ date('d/m/y', strtotime($course->date_start))}} @if($course->date_end) - {{ date('d/m/y', strtotime($course->date_end))}} @endif
                    <a href="{{ route('notif.remind_join', ['id' => $course->id]) }}"><i class="fas fa-bell"></i></a>
                    </td>
                    <td>{{ number_format($course->price) }}</td>
                    <!-- <td></td> -->
                    <td>{{ $course->sum_reg }} <a href="{{ route('notif.remind_confirm', ['id' => $course->id]) }}"><i class="fas fa-bell"></i></a></td>
                    <td>{{ $course->updated_at }}</td>
                    <td>
                        @if($userServ->isMod(\Auth::user()->role))
                        {!! $itemServ->statusOperation($course->id, $course->status) !!}
                        <!-- {!! $itemServ->typeOperation($course) !!} -->
                        @endif
                        
                    </td>
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
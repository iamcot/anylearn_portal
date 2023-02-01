@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.layout')
@section('spmb')
add2cart
@endsection
@section('body')
<!-- <h5 class="text-success mb-5">@lang('Vui lòng hoàn tất các bước sau để đăng ký khóa học')</h5> -->
<form class="" method="get">
    <input type="hidden" name="class" value="{{ $item->id }}">

    <div class="card mb-3 border-left-primary shadow">
        <div class="card-header">
            <h5 class="modal-title m-0 font-weight-bold text-secondary"><i class="fa fa-shopping-cart"></i> @lang('Khóa học bạn đang đăng ký')</h5>
        </div>
        <div class="card-body">
            <h3 class="fw-bold text-success">{{ $item->title }}</h3>
            <p>{{ $author->role == 'teacher' ? __('Giảng viên') : __('Trung tâm') }}: {{ $author->name }}</p>
            <p>@lang('Khai giảng:') {{ date('d/m/Y', strtotime($item->date_start)) }}
                {{ $num_schedule <= 1 ? '' : '(có ' . $num_schedule . ' buổi học)' }}
            </p>
            <p>@lang('Học phí:') <strong>{{ number_format($item->price, 0, ',', '.') }}</strong></p>
            <p>@lang('Bạn sẽ nhận') <strong>{{ number_format($commission, 0, ',', '.') }}</strong>
        </div>
    </div>
    <div class="card mb-3 border-left-primary shadow">
        <div class="card-header">
            <h5 class="modal-title m-0 font-weight-bold text-secondary"><i class="fa fa-user"></i> @lang('Bạn đang đăng ký cho')</h5>
        </div>
        <div class="card-body">

        </div>
    </div>
    <div class="text-center mb-5 mt-5">
        <button name="action" value="saveCart" class="btn btn-success w-50 border-0 font-weight-bold">@lang("ĐĂNG KÝ")</button>
    </div>
</form>
@endsection
@section('jscript')
@parent
@endsection
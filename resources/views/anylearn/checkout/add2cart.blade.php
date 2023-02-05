@inject('itemServ', 'App\Services\ItemServices')
@extends('anylearn.layout')
@section('spmb')
add2cart
@endsection
@section('body')
<!-- <h5 class="text-success mb-5">@lang('Vui lòng hoàn tất các bước sau để đăng ký khóa học')</h5> -->
<form class="" method="get" id="spmc" data-spm="{{ $item->id }}">
    <input type="hidden" name="class" value="{{ $item->id }}">
    <input type="hidden" name="api_token" value="{{ $api_token }}">

    <div class="card mb-3 border-left-primary shadow">
        <div class="card-header">
            <h5 class="modal-title m-0 font-weight-bold text-secondary"><i class="fa fa-shopping-cart"></i>
                @lang('Khóa học bạn đang đăng ký')</h5>
        </div>
        <div class="card-body">
            <h3 class="fw-bold text-success">{{ $item->title }}</h3>
            <p>Đối tác:  {{ $author->name }}</p>
            <p>@lang('Khai giảng:') {{ date('d/m/Y', strtotime($item->date_start)) }}
                {{ $num_schedule <= 1 ? '' : '(có ' . $num_schedule . ' buổi học)' }}
            </p>
            <p>@lang('Học phí:') <strong>{{ number_format($item->price, 0, ',', '.') }}</strong></p>
            <p>@lang('Bạn sẽ nhận') <strong>{{ number_format($commission, 0, ',', '.') }}</strong> @lang('anyPoint cho giao dịch này')</p>
        </div>
    </div>
    <div class="card mb-3 border-left-primary shadow">
        <div class="card-header">
            <h5 class="modal-title m-0 font-weight-bold text-secondary"><i class="fa fa-user"></i> @lang('Bạn đang đăng ký cho')
            </h5>
        </div>
        <div class="card-body">
           
            <div class="form-check">
                <input type="radio" class="form-check-input" id="child0" name="child" value="{{ $user->id }}" checked><label for="child0">{{ $user->name }} (@lang('Tôi'))</label>
            </div>
           
            @if (count($children) > 0)
            @foreach ($children as $child)
            <div class="form-check">
                <input type="radio" class="form-check-input" id="child{{ $child->id }}" name="child" value="{{ $child->id }}"> <label for="child{{ $child->id }}">{{ $child->name }}</label>
            </div>
            @endforeach
            @endif
            <div class="float-right nav-link">
                <button type="button" class="btn btn-success border-0 rounded rounded-pill btn-sm my-2 my-sm-0 ml-2" data-bs-toggle="modal" data-bs-target="#exampleModal">Thêm người học mới</button>
            </div>
        </div>
    </div>
    <div class="card mb-3 border-left-primary shadow">
        <div class="card-header">
            <h5 class="modal-title m-0 font-weight-bold text-secondary"><i class="fa fa-calendar"></i> @lang('Lịch học')
            </h5>
        </div>
        <div class="card-body">
            @if (count($item->openings) > 0)
            <p class="text-danger">@lang('Chọn lịch khai giảng')</p>
            <ul class="list-unstyled">
                @foreach ($item->openings as $opening)
                <li>
                    <label for="class_{{ $opening->id }}"> <input required id="class_{{ $opening->id }}" type="radio" name="class" value="{{ $opening->id }}"> {{ $opening->title }}</label>
                </li>
                @endforeach
            </ul>
            @else 
                <p>Lịch học bắt đầu từ {{ $item->time_start}}, ngày {{ date('d/m/Y', strtotime($item->date_start)) }}</p>
            @endif
        </div>
    </div>
    @if (!empty($extras) && count($extras) > 0)
    <div class="card mb-3 border-left-primary shadow">
        <div class="card-header">
            <h5 class="modal-title m-0 font-weight-bold text-secondary"><i class="fa fa-user"></i> @lang('Chọn phụ phí')
            </h5>
        </div>
        <div class="card-body">
            @foreach ($extras as $extra)
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="extra{{ $extra->id }}" name="extrafee[]" value="{{ $extra->id }}">
                <label for="extra{{ $extra->id }}" class="form-check-label">{{ $extra->title }} - {{ number_format($extra->price)}}₫ </label>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    <div class="text-center mb-5 mt-5">
        <button name="action" value="saveCart" class="btn btn-success w-50 border-0 font-weight-bold">@lang('ĐĂNG KÝ')</button>
    </div>
</form>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title center" id="exampleModalLabel">@lang('Tạo tài khoản con')</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <!-- Form  -->
                <form method="POST">
                    @csrf
                    <input type="hidden" name="api_token" value="{{ $api_token }}">
                    <div class="">
                        <div class="form-group row">
                            <h6><b>@lang('Thông tin cá nhân')</b></h6>
                            <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Họ và tên*') }}</label>
                            <div class="col-md-8">
                                <input id="name" value="{{ !empty($userC) ? $userC->name : null }}" name="username" type="text" class="form-control @error('name') is-invalid @enderror" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="title" class="col-md-3 col-form-label text-md-right">{{ __('Ngày sinh*') }}</label>
                            <div class="col-md-8">
                                <input id="title" value="{{ !empty($userC) ? $userC->dob : null }}" type="date" class="form-control @error('dob') is-invalid @enderror" name="dob">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Giới tính') }}</label>
                            <div class="col-md-8 mt-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="male" name="sex" id="sex" <?php if (empty($userC->sex) || $userC->sex == "male") echo "checked='checked'" ?>>
                                            <label class="form-check-label" for="male">
                                                @lang('Nam')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="female" name="sex" id="sex" <?php if (!empty($userC->sex) && $userC->sex == "female") echo "checked='checked'"; ?>>
                                            <label class="form-check-label" for="female">
                                                @lang('Nữ')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">

                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <h6><b>@lang('Thông tin khác')</b></h6>
                                <label for="introduce" class="col-md-4 col-form-label text-md-right @error('content') is-invalid @enderror">{{ __('Giới thiệu ngắn') }}</label>
                                <br>
                                <div class="col-md-12">
                                    <textarea class="form-control" id="introduce" name="introduce">{!! old('introduce', !empty($userC) ? $userC->introduce : '') !!}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('Hủy')</button>
                            <button type="Submit" class="btn btn-success" name="action" value="createChild">@lang('Lưu')</button>
                        </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
@section('jscript')
@parent
@endsection
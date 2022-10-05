@inject('userServ','App\Services\UserServices')
@extends('anylearn.me.layout')

@section('body')
<div class="row">
    <div class="col-md-8">
        <form method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ !empty($user) ? $user->id : null }}">
            <div class="card shadow">
                <div class="card-body">
                    <div class="">
                        <div class="form-group row">
                            <div class="container">
                                <div class="row">
                                    <div class="panel">
                                        <div class="cover-photo">
                                            @if(auth()->user()->banner != null)
                                            <div class="fb-timeline-img">
                                                <img src="{{ auth()->user()->banner }}" alt="">
                                            </div>
                                            @else
                                            <div class="fb-timeline-img">
                                                <img src="http://anylearn.vn/cdn/anylearn/img/logo-color.svg" alt="">
                                            </div>
                                            @endif
                                        </div>
                                        <div class="panel-body">
                                            @if(auth()->user()->image != null)
                                            <div class="profile-thumb">
                                                <img src="{{ auth()->user()->image }}" alt="">
                                            </div>
                                            <h2 style="margin-top:5px"><a class="text-black">{{ Auth::user()->name }}</a></h2>
                                            @else
                                            <div class="profile-thumb">
                                                <img src="http://anylearn.vn/cdn/anylearn/img/logo-color.svg" alt="">
                                            </div>
                                            <h2><a class="text-black">{{ Auth::user()->name }}</a></h2>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <h6><b>@lang('Thông tin cá nhân')</b></h6>
                            <label for="name" class="col-md-3 col-form-label text-md-right text-start">{{ __('Họ và tên*') }}</label>
                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', !empty($user) ? $user->name : '') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="title" class="col-md-3 col-form-label text-md-right text-start">{{ __('Ngày sinh*') }}</label>
                            <div class="col-md-8">
                                <input id="title" type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" value="{{ old('dob', !empty($user) ? $user->dob : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right text-start">{{ __('Giới tính') }}</label>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="male" name="sex" id="sex" <?php if (empty($user->sex) || $user->sex == "male") echo "checked='checked'" ?>>
                                            <label class="form-check-label" for="male">
                                                @lang('Nam')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="female" name="sex" id="sex" <?php if (!empty($user->sex) && $user->sex == "female") echo "checked='checked'"; ?>>
                                            <label class="form-check-label" for="female">
                                                @lang('Nữ')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="other" name="sex" id="sex" <?php if (!empty($user->sex) && $user->sex == "other") echo "checked='checked'" ?>>
                                            <label class="form-check-label" for="other">
                                                @lang('Khác')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right text-start">{{ __('CMND/CCCD') }}</label>
                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control @error('cert_id') is-invalid @enderror" name="cert_id" value="{{ old('cert_id', !empty($user) ? $user->cert_id : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right text-start">{{ __('Mã giới thiệu') }}</label>
                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control @error('refcode') is-invalid @enderror" name="refcode" value="{{ old('refcode', !empty($user) ? $user->refcode : '') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-md-3 col-form-label text-md-right text-start">{{ __('Số điện thoại') }}</label>
                            <div class="col-md-8">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', !empty($user) ? $user->phone : '') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <h6><b>@lang('Thông tin liên hệ')</b></h6>
                            <label for="email" class="col-md-3 col-form-label text-md-right text-start">{{ __('Email') }}</label>
                            <div class="col-md-8">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', !empty($user) ? $user->email : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 col-form-label text-md-right text-start">{{ __('Address') }}</label>
                            <div class="col-md-8">
                                <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address', !empty($user) ? $user->address : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-md-3 col-form-label text-md-right text-start">{{ __('Avatar') }}</label>
                            <div class="col-md-4">
                                <input name="image" type="file" id="image" />
                            </div>
                            <div class="col-md-4">
                                <!-- @if($user->image)
                        <img src="{{ $user->image }}" alt="" style="height: 50px;">
                    @endif -->
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-md-3 col-form-label text-md-right text-start">{{ __('Banner') }}</label>
                            <div class="col-md-4">
                                <input name="banner" type="file" id="image" />
                            </div>
                            <div class="col-md-4">
                                <!-- <br>
                    @if($user->banner)
                        <img src="{{ $user->banner }}" alt="" style="height: 50px;">
                    @endif -->
                            </div>
                        </div>
                        <div class="form-group row">
                            <h6><b>@lang('Thông tin khác')</b></h6>
                            <label for="introduce" class="col-md-3 col-form-label text-md-right text-start @error('content') is-invalid @enderror">{{ __('Giới thiệu ngắn') }}</label>
                            <div class="col-md-8">
                                <textarea class="form-control" id="introduce" name="introduce">{!! old('introduce', !empty($user) ? $user->introduce : '') !!}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <h6><b>@lang('Thông tin khác (Tiếng Anh)')</b></h6>
                            <label for="introduce" class="col-md-3 col-form-label text-md-right text-start @error('content') is-invalid @enderror">{{ __('Giới thiệu ngắn') }}</label>
                            <div class="col-md-8">
                                <textarea class="form-control" id="introduce" name="introduce">{!! old('introduce', !empty($user) ? $user->introduce : '') !!}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button name="save" value="save" class="btn btn-success rounded-pill shadow border-0"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-4">
        @include('anylearn.me.friend_list')
    </div>
</div>


@endsection
@section('jscript')
@parent
<script src="/cdn/vendor/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor');
</script>
@endsection
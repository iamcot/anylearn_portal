@extends('register.layout')
@section('body')
<div class="row">
    <div class="col-lg-5 introduce text-center">
        @yield('introduce')
    </div>
    <div class="col-lg-7 register_form">
        @if(!isset($isReg))
        <h2 class="text-light text-center">@yield('header1')</h2>
        <br>
        <h5 class="text-light text-center">@yield('header2')</h5>
        @if(env('LOGIN_3RD_ENABLE', 0))
        <div class="text-center">
            <a class="btn btn-primary mt-2 text-center border shadow" href="/login/facebook?ref={{ !empty($user) ? $user->refcode : old('ref') }}">
                <i class="fab fa-facebook-f"></i></a>
        </div>
        <br>
        <p class="text-light text-center">--- HOẶC ---</p>
        @endif
        <div class="col-12 p1 text-light">
            <form method="POST">
                @csrf
                <input id="ref" type="hidden" name="ref" value="{{ !empty($user) ? $user->refcode : old('ref') }}">
                <input type="hidden" name="role" value="{{ $role }}">
                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-end" id="label_name">{{ __('Họ và Tên') }} *</label>
                    <div class="col-md-8">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-md-4 col-form-label text-md-end">{{ __('Số điện thoại') }} *</label>
                    <div class="col-md-8">
                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                        @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>
                    <div class="col-md-8">
                        <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Mật khẩu') }} *</label>

                    <div class="col-md-8">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Nhập lại mật khẩu') }} *</label>

                    <div class="col-md-8">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="text-center">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" id="toc" required checked>
                            <label class="form-check-label" for="toc">@lang('Tôi đã đọc và đồng ý với <a href="#" id="toc-action" data-url=":url">Điều khoản sử dụng</a>', ['url' => route('ajax.toc')])</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row mb-0">
                    <div class="text-center">
                        <button type="submit" class="btn btn-success border border-2">
                            <i class="fas fa-sign-in-alt"></i> {{ __('Đăng ký') }}
                        </button>
                        <a href="{{ url()->full() }}&has-account=1">Tôi đã đăng ký</a>
                    </div>
                </div>
            </form>
        </div>


        @else
        <div class="row p1" style="margin-top:100px;">
        <h5 class="text-light text-center">Bạn vừa hoàn thành đăng ký tài khoản trên anyLEARN, hãy tải ứng dụng về máy và bắt đầu trải nghiệm!</h5>
            <div class="col-md-6" style="padding: 30px;">
                <a href="itms-apps://apps.apple.com/vn/app/anylearn/id1518155412">
                    <img src="/cdn/onepage/images/ios.png" style="width:100%" alt="">
                </a>
            </div>
            <div class="col-md-6" style="padding: 30px;">
                <a href="market://details?id=vn.anylearn">
                    <img src="/cdn/onepage/images/android.png" style="width:100%" alt="">
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@include('dialog.toc')
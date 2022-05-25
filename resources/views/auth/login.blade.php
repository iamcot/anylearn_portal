@extends('auth.layout')
@section('body')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8" style="padding-top:100px;">
            <div class="card shadow">
                <div class="card-header font-weight-bold text-{{ env('MAIN_COLOR', 'primary') }}"><i class="fas fa-shield-alt"></i> {{ __('Đăng nhập vào') }} {{ env('APP_NAME') }}
                <a class="btn btn-link font-weight-bold text-success float-right" href="/ref/anylearn">ĐĂNG KÝ</a>
            </div>

                <div class="card-body">

                    @if(session('notify'))
                    <h5 class="text-center text-danger">
                        {{ session('notify') }}
                    </h5>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Số điện thoại') }}</label>

                            <div class="col-md-6">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>

                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Mật khẩu') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Tự động đăng nhập lần sau') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-{{ env('MAIN_COLOR', 'primary') }}">
                                    <i class="fas fa-sign-in-alt"></i> {{ __('Đăng nhập') }}
                                </button>

                                @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.otp') }}">
                                    {{ __('Quên mật khẩu') }}
                                </a>
                                @endif
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@inject('userServ','App\Services\UserServices')
@extends('layout')

@section('body')
<form method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($user) ? $user->id : null }}">
    <div class="card shadow">
        <div class="card-body">
            <div class="col-lg-6">
                <div class="form-group row">
                    <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Họ và tên') }}</label>
                    <div class="col-md-8">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', !empty($user) ? $user->name : '') }}" required >
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Số điện thoại') }}</label>
                    <div class="col-md-8">
                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', !empty($user) ? $user->phone : '') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>
                    <div class="col-md-8">
                        <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', !empty($user) ? $user->email : '') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="commission_rate" class="col-md-4 col-form-label text-md-right">{{ __('Tỉ lệ hoa hồng') }}</label>
                    <div class="col-md-8">
                        <input id="commission_rate" type="text" class="form-control @error('commission_rate') is-invalid @enderror" name="commission_rate" value="{{ old('commission_rate', !empty($user) ? $user->commission_rate : '') }}" required>
                        <small>Tỉ lệ thành viên sẽ được nhận về từ doanh thu. Dạng thập phân</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="role" class="col-md-4 col-form-label text-md-right">{{ __('Vai trò') }}</label>
                    <div class="col-md-8">
                        <select class="form-control" name="role" id="role">
                            @foreach($type == 'mod' ? \App\Constants\UserConstants::$modRoles : \App\Constants\UserConstants::$memberRoles  as $role)
                            <option value="{{ $role }}" {{ !empty($user) && $user->role  == $role ? 'selected' : '' }} >{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Mật khẩu') }}</label>
                    <div class="col-md-8">
                        <input id="password" type="password" class="form-control" name="password" value="" {{ empty($user) ? 'required' : '' }}>
                        @if(!empty($user))
                            <span class="small">*Để trống nếu không thay đổi</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button name="save" value="save" class="btn btn-{{ env('MAIN_COLOR', 'primary') }}"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
        </div>
    </div>
</form>
@endsection
@inject('userServ','App\Services\UserServices')
@extends('layout')

@section('rightFixedTop')
<form class="row pt-4">
    <div class="col-xs-2 mr-1 text-success">
        <div>
            <div>Ví M: {{ $user->wallet_m }}</div>
            <div>Ví C: {{ $user->wallet_c }}</div>
        </div>

    </div>
    <div class="col-xs-2 mr-1">
        <a class="btn btn-primary" id="moneyfix-action" href="#"><i class="fas fa-comments-dollar"></i> @lang('Giao dịch tiền')</a>
    </div>
</form>
@endsection

@section('body')
<form method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($user) ? $user->id : null }}">
    <div class="card shadow">
        <div class="card-body">
            <div class="">
                <div class="form-group row">
                    <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Họ và tên') }}</label>
                    <div class="col-md-8">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', !empty($user) ? $user->name : '') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-md-2 col-form-label text-md-right">{{ __('Số điện thoại') }}</label>
                    <div class="col-md-8">
                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', !empty($user) ? $user->phone : '') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="boost_score" class="col-md-2 col-form-label text-md-right">{{ __('Boost Score') }}</label>
                    <div class="col-md-8">
                        <input id="boost_score" type="text" class="form-control @error('boost_score') is-invalid @enderror" name="boost_score" value="{{ old('boost_score', !empty($user) ? $user->boost_score : '') }}">
                        <small>Số càng lớn càng được ưu tiên</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="user_id" class="col-md-2 col-form-label text-md-right">{{ __('ID người giới thiệu') }}</label>
                    <div class="col-md-8">
                        <input id="user_id" type="text" class="form-control @error('user_id') is-invalid @enderror" name="user_id" value="{{ old('user_id', !empty($user) ? $user->user_id : '') }}">
                        <small>Có thể tham khảo ID ngoài danh sách thành viên.</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-md-2 col-form-label text-md-right">{{ __('Email') }}</label>
                    <div class="col-md-8">
                        <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', !empty($user) ? $user->email : '') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="commission_rate" class="col-md-2 col-form-label text-md-right">{{ __('Tỉ lệ hoa hồng') }}</label>
                    <div class="col-md-8">
                        <input id="commission_rate" type="text" class="form-control @error('commission_rate') is-invalid @enderror" name="commission_rate" value="{{ old('commission_rate', !empty($user) ? $user->commission_rate : '') }}" required>
                        <small>Tỉ lệ thành viên sẽ được nhận về từ doanh thu. Dạng thập phân</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="role" class="col-md-2 col-form-label text-md-right">{{ __('Vai trò') }}</label>
                    <div class="col-md-8">
                        <select class="form-control" name="role" id="role">
                            @foreach($type == 'mod' ? \App\Constants\UserConstants::$modRoles : \App\Constants\UserConstants::$memberRoles as $role)
                            <option value="{{ $role }}" {{ !empty($user) && $user->role  == $role ? 'selected' : '' }}>{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-md-2 col-form-label text-md-right">{{ __('Mật khẩu') }}</label>
                    <div class="col-md-8">
                        <input id="password" type="password" class="form-control" name="password" value="" {{ empty($user) ? 'required' : '' }}>
                        @if(!empty($user))
                        <span class="small">*Để trống nếu không thay đổi</span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="editor" class="col-md-2 col-form-label text-md-right font-weight-bold @error('content') is-invalid @enderror">{{ __('Thông tin giới thiệu') }}</label>
                    <div class="col-md-8">
                        <textarea id="editor" name="full_content">{!! old('content', !empty($user) ? $user->full_content : '') !!}</textarea>
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
@include('dialog.money_fix')
@section('jscript')
@parent
<script src="/cdn/vendor/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor');
    $('#moneyfix-action').click(function() {
        $('#moneyFixModal').modal('show');
    });
</script>
@endsection
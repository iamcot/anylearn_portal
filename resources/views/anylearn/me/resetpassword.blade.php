@inject('userServ','App\Services\UserServices')
@extends('anylearn.me.layout')
@section('body')
<form method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($user) ? $user->id : null }}">
    <div class="card shadow">
        <div class="card-body">
            <div class="">
                <div class="form-group row">
                    <label for="name" class=" col-md-3 col-form-label text-start">{{ __('Mật Khẩu*') }}</label>
                    <div class="col-md-9">
                        <div class="input-group mb-3">
                            <input type="password" name="password" id="password" class="@error('password') is-invalid @enderror form-control" placeholder="Nhập mật khẩu hiện tại" aria-label="Nhập mật khẩu hiện tại" aria-describedby="button-addon2" required>
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            
                            
                        </div>
                            @if (\Session::has('error'))
                            <span class="text-danger ml-2">{!! \Session::get('error') !!}</span>
                            @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-md-3 col-form-label text-start">{{ __('Mật Khẩu Mới*') }}</label>
                    <div class="col-md-9">
                        <div class="input-group mb-3">
                            <input type="password" name="newpassword" id="newpassword" class="@error('password') is-invalid @enderror form-control" placeholder="Nhập mật khẩu mới" aria-label="Nhập mật khẩu mới" aria-describedby="button-addon2" required>
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                     
                    <label for="name" class="  col-md-3 col-form-label text-start">{{ __('Xác Nhận Mật Khẩu*') }}</label>
                    <div class="col-md-9">
                        <div class="input-group mb-3">
                            <input type="password" name="repassword" id="repassword" class="@error('password') is-invalid @enderror form-control" placeholder="Xác nhận mật khẩu" aria-label="Xác nhận mật khẩu" aria-describedby="button-addon2" required>
                            @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        @if (\Session::has('errormk'))
                            <span class="text-danger ml-2">{!! \Session::get('errormk') !!}</span>
                            @endif
                        <span id='message'></span>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="card-footer text-center">
            <button name="save" value="save" class="btn btn-success rounded-pill shadow border-0"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
        </div>
    </div>
</form>
@endsection
@section('jscript')
@parent
<script>
    $('#newpassword, #repassword').on('keyup', function () {
  if ($('#newpassword').val() == $('#repassword').val()) {
    $('#message').html(null);
  } else 
    $('#message').html('Mật khẩu không khớp').css('color', 'red');
});
</script>
@endsection
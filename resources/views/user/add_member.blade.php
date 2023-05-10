@inject('userServ','App\Services\UserServices')
@extends('layout')

@section('body')
<div class="card mb-3 shadow">
    <div class="card-body">
        <form>
            <div class="form-group row">
                <label for="name" class="col-md-3 col-form-label text-md-right ">{{ __('Họ và tên') }}</label>
                <div class="col-md-8">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="phone" class="col-md-3 col-form-label text-md-right ">{{ __('Số điện thoại') }}</label>
                <div class="col-md-8">
                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="col-md-3 col-form-label text-md-right ">{{ __('Mật khẩu') }}</label>
                <div class="col-md-8">
                    <input id="password" type="text" class="form-control @error('password') is-invalid @enderror" name="password" value="{{ old('password') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="address" class="col-md-3 col-form-label text-md-right ">{{ __('Khu vực') }}</label>
                <div class="col-md-8">
                    <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="source" class="col-md-3 col-form-label text-md-right ">{{ __('Nguồn data') }}</label>
                <div class="col-md-8">
                    <input id="source" type="text" class="form-control @error('source') is-invalid @enderror" name="source" value="{{ old('source') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="note" class="col-md-3 col-form-label text-md-right ">{{ __('Mô tả nhu cầu') }}</label>
                <div class="col-md-8">
                    <textarea id="note" class="form-control @error('note') is-invalid @enderror" name="note" value="{{ old('note') }}" rows="3" required></textarea>
                </div>
            </div>
            <div class="text-center mb-3">
                <button class="btn btn-primary border-0 rounded" name="action" value="addMember"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
            </div>
        </form>
    </div>
</div>
@endsection
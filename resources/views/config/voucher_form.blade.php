@extends('layout')


@section('body')
<form method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($voucher) ? $voucher->id : null }}">
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group row">
                <label for="voucher" class="col-md-2 col-form-label text-md-right">{{ __('Voucher') }}</label>
                <div class="col-md-6">
                    <input id="voucher" type="text" class="form-control @error('voucher') is-invalid @enderror" name="voucher" value="{{ old('voucher', !empty($voucher) ? $voucher->voucher : '') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="value" class="col-md-2 col-form-label text-md-right">{{ __('Giá trị') }}</label>
                <div class="col-md-6">
                    <input id="value" type="text" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ old('value', !empty($voucher) ? $voucher->value : '') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="amount" class="col-md-2 col-form-label text-md-right">{{ __('Số lượng') }}</label>
                <div class="col-md-6">
                    <input id="amount" type="text" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount', !empty($voucher) ? $voucher->amount : '') }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="expired" class="col-md-2 col-form-label text-md-right">{{ __('Đến ngày') }}</label>
                <div class="col-md-6">
                    <input id="expired" type="text" class="form-control @error('expired') is-invalid @enderror" name="expired" value="{{ old('expired', !empty($voucher) ? $voucher->expired : '') }}">
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button name="save" value="save" class="btn btn-{{ env('MAIN_COLOR', 'primary') }}"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
        </div>
    </div>
</form>
@endsection
@extends('layout')


@section('body')
<form method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($voucher) ? $voucher->id : null }}">
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group row">
                <label for="voucher" class="col-md-2 col-form-label text-md-right">{{ __('Loại voucher') }}</label>
                <div class="col-md-6">
                    <select name="voucher_type" id="voucher_type" class="form-control">
                        <option value="{{ App\Models\VoucherGroup::TYPE_MONEY }}">Tiền mặt</option>
                        <option value="{{ App\Models\VoucherGroup::TYPE_CLASS }}">Khóa học</option>
                    </select>
                </div>
            </div>
            <div class="form-group row" id="extra_box" style="display: none;">
                <label for="ext" class="col-md-2 col-form-label text-md-right">{{ __('ID Khóa học') }}</label>
                <div class="col-md-6">
                    <input id="ext" type="text" class="form-control @error('expired') is-invalid @enderror" name="ext" value="{{ old('ext', !empty($voucher) ? $voucher->ext : '') }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="generate_type" class="col-md-2 col-form-label text-md-right">{{ __('Cách tạo') }}</label>
                <div class="col-md-6">
                <select name="generate_type" id="generate_type" class="form-control">
                        <option value="{{ App\Models\VoucherGroup::GENERATE_MANUALLY }}">Mã giống nhau</option>
                        <option value="{{ App\Models\VoucherGroup::GENERATE_AUTO }}">Tự động tạo mã khác nhau</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="prefix" class="col-md-2 col-form-label text-md-right">{{ __('Voucher/Tiền tố') }}</label>
                <div class="col-md-6">
                    <input id="prefix" type="text" class="form-control @error('voucher') is-invalid @enderror" name="prefix" value="{{ old('prefix', !empty($voucher) ? $voucher->prefix : '') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="value" class="col-md-2 col-form-label text-md-right">{{ __('Giá trị') }}</label>
                <div class="col-md-6">
                    <input id="value" type="text" class="form-control @error('value') is-invalid @enderror" name="value" value="{{ old('value', !empty($voucher) ? $voucher->value : '') }}" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="qtt" class="col-md-2 col-form-label text-md-right">{{ __('Số lượng') }}</label>
                <div class="col-md-6">
                    <input id="qtt" type="text" class="form-control @error('amount') is-invalid @enderror" name="qtt" value="{{ old('qtt', !empty($voucher) ? $voucher->qtt : '') }}">
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="expired" class="col-md-2 col-form-label text-md-right">{{ __('Đến ngày') }}</label>
                <div class="col-md-6">
                    <input id="expired" type="text" class="form-control @error('expired') is-invalid @enderror" name="expired" value="{{ old('expired', !empty($voucher) ? $voucher->expired : '') }}">
                </div>
            </div> -->
        </div>
        <div class="card-footer">
            <button name="save" value="save" class="btn btn-{{ env('MAIN_COLOR', 'primary') }}"><i class="fas fa-save"></i> @lang('Tạo mới')</button>
        </div>
    </div>
</form>
@endsection
@section('jscript')
<script>
$("#voucher_type").change(function() {
    if ($(this).val() == "{{ App\Models\VoucherGroup::TYPE_MONEY }}") {
        $("#extra_box").hide();
    } else {
        $("#extra_box").show();
    }
});
</script>
@endsection
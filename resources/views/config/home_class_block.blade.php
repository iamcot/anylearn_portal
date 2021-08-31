<li class="">
    @if($index > 0)
    <hr>
    @endif
    <div class="form-group row">
        <label for="voucher" class="col-md-2 col-form-label text-md-right">{{ __("Tiêu đề") }}</label>
        <div class="col-md-6">
            <input id="voucher" type="text" class="form-control" name="block[{{ $index }}][title]" value="{{ !empty($config) ? $config['title'] : '' }}" >
            <p class="small text-danger">Để trống nếu không muốn block này hiện lên</p>
        </div>
    </div>
    <div class="form-group row">
        <label for="value" class="col-md-2 col-form-label text-md-right">{{ __('IDs khoá học') }}</label>
        <div class="col-md-6">
            <input id="value" type="text" class="form-control" name="block[{{ $index }}][classes]" value="{{ !empty($config) ? $config['classes'] : '' }}" >
            <p class="small text-danger">Các IDs class cách nhau bằng dấu phẩy ",". Ví dụ: 1,20,199</p>
        </div>
    </div>
</li>
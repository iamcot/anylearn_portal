<li class="">
    @if($index > 0)
    <hr>
    @endif
    <ul class="nav nav-tabs" id="i18ntab" role="tablist">
        @foreach (App\Models\I18nContent::$supports as $locale)
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link {{ $locale == App\Models\I18nContent::DEFAULT ? 'active' : '' }}"
                    id="{{ $locale }}-tab" data-toggle="tab" data-bs-toggle="tab"
                    data-target="#{{ $locale }}box{{ $index }}" data-bs-target="#{{ $locale }}box{{ $index }}"
                    type="button" role="tab" aria-controls="{{ $locale }}"
                    aria-selected="{{ $locale == App\Models\I18nContent::DEFAULT ? 'true' : 'false' }}">{{ $locale }}</button>
            </li>
        @endforeach
    </ul>
    <div class="tab-content" id="i18ntabContent">
        @foreach (App\Models\I18nContent::$supports as $locale)
            <div class="p-3 tab-pane fade {{ $locale == App\Models\I18nContent::DEFAULT ? 'show active' : '' }}"
                id="{{ $locale }}box{{ $index }}" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
    <div class="form-group row">
        <label for="voucher{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ __("Tiêu đề") }}[{{ $locale }}]</label>
        <div class="col-md-6">
            <input id="voucher{{ $locale }}" type="text" class="form-control" name="block[{{ $index }}][title][{{ $locale }}]" value="{{ !empty($config) ? $config['title'][$locale] : '' }}" >
            <p class="small text-danger">Để trống nếu không muốn block này hiện lên</p>
        </div>
    </div>
</div>
@endforeach
</div>
    <div class="form-group row">
        <label for="value" class="col-md-2 col-form-label text-md-right">{{ __('IDs khoá học') }}</label>
        <div class="col-md-6">
            <input id="value" type="text" class="form-control" name="block[{{ $index }}][classes]" value="{{ !empty($config) ? $config['classes'] : '' }}" >
            <p class="small text-danger">Các IDs class cách nhau bằng dấu phẩy ",". Ví dụ: 1,20,199</p>
        </div>
    </div>
</li>

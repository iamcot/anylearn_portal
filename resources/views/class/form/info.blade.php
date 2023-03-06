@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')
<div class="card mb-3 shadow">
    <div class="card-header font-weight-bold">Thông tin chung</div>
    <div class="card-body">
        <div class="form-group row">
            <label for="title" class="col-md-3 col-form-label text-md-right ">{{ __('Tiêu đề') }}</label>
            <div class="col-md-8">
                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title[{{ App\Models\I18nContent::DEFAULT }}]" value="{{ old('title', !empty($course) ? $course['info']->title[App\Models\I18nContent::DEFAULT] : '') }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="categories" class="col-md-3 col-form-label text-md-right ">{{ __('Lĩnh vực') }}</label>
            <div class="col-md-8">
                <select class="form-control" name="categories[]" multiple>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ !empty($itemCategories) && in_array($category->id, $itemCategories) ? 'selected' : '' }}>
                        {{ $category->title }}
                    </option>
                    @endforeach
                </select>
                <p class="small">@lang('Có thể chọn nhiều chuyên mục bằng giữ phím Ctrl (hoặc Cmd)')</p>
            </div>
        </div>

        <div class="form-group row">
            <label for="date_start" class="col-md-3 col-form-label text-md-right ">{{ __('Ngày bắt đầu') }}</label>
            <div class="col-md-8">
                <input id="date_start" type="date" class="form-control @error('date_start') is-invalid @enderror" name="date_start" value="{{ old('date_start', !empty($course) ? $course['info']->date_start : '') }}">
            </div>
        </div>
        <div class="form-group row">
            <label for="time_start" class="col-md-3 col-form-label text-md-right ">{{ __('Thời gian bắt đầu') }}</label>
            <div class="col-md-8">
                <input id="time_start" type="time" class="time form-control @error('time_start') is-invalid @enderror" name="time_start" value="{{ old('time_start', !empty($course) ? $course['info']->time_start : '') }}" placeholder="hh:mm" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="nolimit_time" class="col-md-3 col-form-label text-md-right ">{{ __('Không giới hạn thời gian đăng ký') }}</label>
            <div class="col-md-8 form-check form-switch m-2">
                <input class="form-check-input" type="checkbox" name="nolimit_time" id="nolimit_time" {{ !empty($course) && $course['info']->nolimit_time > 0 ? "checked" : "" }}>
            </div>
        </div>
        <div class="form-group row">
            <label for="nolimit_time" class="col-md-3 col-form-label text-md-right ">{{ __('Có học thử') }}</label>
            <div class="col-md-8 form-check form-switch m-2">
                <input class="form-check-input" type="checkbox" name="nolimit_time" id="nolimit_time" {{ !empty($course) && $course['info']->nolimit_time > 0 ? "checked" : "" }}>
            </div>
        </div>
        <div class="form-group row">
            <label for="nolimit_time" class="col-md-3 col-form-label text-md-right ">{{ __('Có Tham quan trường') }}</label>
            <div class="col-md-8 form-check form-switch m-2">
                <input class="form-check-input" type="checkbox" name="nolimit_time" id="nolimit_time" {{ !empty($course) && $course['info']->nolimit_time > 0 ? "checked" : "" }}>
            </div>
        </div>
        <div class="form-group row">
            <label for="nolimit_time" class="col-md-3 col-form-label text-md-right ">{{ __('Có test đầu vào') }}</label>
            <div class="col-md-8 form-check form-switch m-2">
                <input class="form-check-input" type="checkbox" name="nolimit_time" id="nolimit_time" {{ !empty($course) && $course['info']->nolimit_time > 0 ? "checked" : "" }}>
            </div>
        </div>

        <div class="form-group row">
            <label for="age-range" class="col-md-3 col-form-label text-md-right ">{{ __('Độ tuổi') }}</label>
            <div class="col-md-8">
                <input type="text" id="age-amount" name="ages_range" readonly style="border:0;" data-ages_min="{{ !empty($course['info']->ages_min) ? $course['info']->ages_min : 0 }}" data-ages_max="{{ !empty($course['info']->ages_max) ? $course['info']->ages_max : 30 }}">
                <div id="age-range" class="mt-2"></div>
            </div>
        </div>

        <div class="form-group row">
            <label for="tags" class="col-md-3 col-form-label text-md-right ">{{ __('Tags') }}</label>
            <div class="col-md-8">
                <input id="tags" type="text" class="form-control @error('tags') is-invalid @enderror" name="tags" value="{{ old('tags', !empty($course) ? $course['info']->tags : '') }}">
            </div>
        </div>

        <div class="form-group row" id="locationbox" {{ !empty($course) && !empty($course['info']->subtype) && $course['info']->subtype != \App\Constants\ItemConstants::SUBTYPE_ONLINE ? 'style=display:none' : '' }}>
            <label for="location" class="col-md-3 col-form-label text-md-right ">{{ __('URL Room học') }}</label>
            <div class="col-md-8">
                <input id="location_type" type="hidden" name="location_type" value="offline">
                <input id="location" type="text" class="form-control @error('location') is-invalid @enderror" name="location" value="{{ old('location', !empty($course) ? $course['info']->location : '') }}">
            </div>
        </div>
    </div>
</div>
<div class="card mb-3 shadow">
    <div class="card-header font-weight-bold">Ảnh đại diện</div>
    <div class="card-body">
        <input name="image" type="file" id="mainImageInp" />
        <div id="mainImage" class="mt-2">
            @if(!empty($course) && $course['info']->image)
            <img src="{{ $course['info']->image }}" alt="$course['info']->title" class="img-fluid">
            @endif
        </div>
    </div>
</div>
<div class="card mb-3 shadow">
    <div class="card-header font-weight-bold">Tuỳ chỉnh SEO</div>
    <div class="card-body">
        <div class="form-group row">
            <label for="seo_title" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Tiêu đề') }}</label>
            <div class="col-md-8">
                <input onchange="formatSlug(this, 'seo_url')" id="seo_title" type="text" class="form-control @error('seo_title') is-invalid @enderror" name="seo_title" value="{{ old('seo_title', !empty($course) ? $course['info']->seo_title : '') }}">
            </div>
        </div>

        <div class="form-group row">
            <label for="seo_url" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Đường dẫn') }}</label>
            <div class="col-md-8">
                <input id="seo_url" type="text" class="form-control @error('seo_url') is-invalid @enderror" name="seo_url" value="{{ old('seo_url', !empty($course) ? $course['info']->seo_url : '') }}">
            </div>
        </div>

        <div class="form-group row">
            <label for="seo_desc" class="col-md-3 col-form-label text-md-right font-weight-bold">{{ __('Description') }}</label>
            <div class="col-md-8">
                <textarea name="seo_desc" class="form-control">{{ old('seo_desc', !empty($course) ? $course['info']->seo_desc : '') }}</textarea>
            </div>
        </div>
    </div>
</div>

<div class="text-center mb-3">
    <button class="btn btn-success border-0 rounded" name="tab" value="info"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
    <a href="javascript:changeTab('price-tab')" class="btn btn-primary border-0 rounded">Sửa học phí >></a>
</div>
@include('dialog.company_commission')
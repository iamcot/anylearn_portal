<div class="row">
    <div class="col-12 p-5">
        <h4>@lang('Tùy chỉnh SEO')</h4>
        <hr />
        <div class="form-group row">
            <label for="name" class="col-md-2 col-form-label text-md-right font-weight-bold">{{ __('Tiêu đề') }}</label>
            <div class="col-md-10">
                <input id="name" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-2 col-form-label text-md-right font-weight-bold">{{ __('Đường dẫn') }}</label>
            <div class="col-md-10">
                <input id="name" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-2 col-form-label text-md-right font-weight-bold">{{ __('Description') }}</label>
            <div class="col-md-10">
                <textarea name="short_content" class="form-control" ></textarea>
            </div>
        </div>

    </div>
</div>
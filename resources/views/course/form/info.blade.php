<div class="row">
    <div class="col-12 p-5">
        <h4>@lang('Thông tin cơ bản khóa học')</h4>
        <hr />
        <div class="form-group row">
            <label for="name" class="col-md-2 col-form-label text-md-right font-weight-bold">{{ __('Tiêu đề') }}</label>
            <div class="col-md-10">
                <input id="name" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-2 col-form-label text-md-right font-weight-bold">{{ __('Học phí') }}</label>
            <div class="col-md-10">
                <input id="name" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-2 col-form-label text-md-right font-weight-bold">{{ __('Thời gian') }}</label>
            <div class="col-md-10">
                <input id="name" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-2 col-form-label text-md-right font-weight-bold">{{ __('Thông tin tóm tắt') }}</label>
            <div class="col-md-10">
                <textarea name="short_content" class="form-control" ></textarea>
            </div>
        </div>

        <div class="form-group row">
            <label for="name" class="col-md-2 col-form-label text-md-right font-weight-bold">{{ __('Thông tin giới thiệu') }}</label>
            <div class="col-md-10">
                <textarea id="editor" name="content"></textarea>
            </div>
        </div>

    </div>
</div>
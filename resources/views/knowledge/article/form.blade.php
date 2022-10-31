@extends('layout')
@section('body')
<form method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($article) ? $article->id : null }}">
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group row">
                <label for="category_id" class="col-md-2 col-form-label text-md-right">{{ __('Thư mục') }}</label>
                <div class="col-md-6">
                    <select name="knowledge_category_id" id="category_id" required class="form-control">
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ isset($article) && $cat->id == $article->knowledge_category_id ? 'selected' : ''}}>{{ $cat->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <ul class="nav nav-tabs" id="i18ntab" role="tablist">
                @foreach (App\Models\I18nContent::$supports as $locale)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $locale == App\Models\I18nContent::DEFAULT ? 'active' : '' }}" id="{{ $locale }}-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#{{ $locale }}box" data-bs-target="#{{ $locale }}box" type="button" role="tab" aria-controls="{{ $locale }}" aria-selected="{{ $locale == App\Models\I18nContent::DEFAULT ? 'true' : 'false' }}">{{ $locale }}</button>
                </li>
                @endforeach
            </ul>
            <div class="tab-content" id="i18ntabContent">
                @foreach(App\Models\I18nContent::$supports as $locale)
                <div class="p-3 tab-pane fade {{ $locale == App\Models\I18nContent::DEFAULT ? 'show active' : '' }}" id="{{ $locale }}box" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                    <div class="form-group row">
                        <label for="title{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ __('Tiêu đề') }} [{{ $locale }}]</label>
                        <div class="col-md-6">
                            <input id="title{{ $locale }}" type="text" class="form-control @error('title') is-invalid @enderror" name="title[{{ $locale }}]" value="{{ old('title', !empty($article) ? $article->title[$locale] : '') }}"  required>
                        </div>
                    </div>
                    <div class="form-group row">
                            <label for="editor{{ $locale }}" class="col-md-2 col-form-label text-md-right @error('content') is-invalid @enderror">{{ __('Nội dung') }} [{{ $locale }}]</label>
                            <div class="col-md-8">
                                <textarea id="editor{{ $locale }}" name="content[{{ $locale }}]">{!! old('content', !empty($article) ? $article->content[$locale] : '') !!}</textarea>
                            </div>
                    </div>
                    <div class="form-group row">
                            <label for="editor_bot{{ $locale }}" class="col-md-2 col-form-label text-md-right @error('content_bot') is-invalid @enderror">{{ __('Nội dung riêng cho bot') }} [{{ $locale }}]
                                <p class="small">Bỏ trống nếu giống nội dung chính</p></label>
                            <div class="col-md-8">
                                <textarea id="editor_bot{{ $locale }}" name="content_bot[{{ $locale }}]">{!! old('content_bot', !empty($article) ? $article->content_bot[$locale] : '') !!}</textarea>
                            </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
        <div class="card-footer">
            <button name="save" value="save" class="btn btn-primary }}"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
        </div>
    </div>
</form>
@endsection
@section('jscript')
@parent
<script src="/cdn/vendor/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editorvi');
    CKEDITOR.replace('editor_botvi');
    CKEDITOR.replace('editoren');
    CKEDITOR.replace('editor_boten');
</script>
@endsection

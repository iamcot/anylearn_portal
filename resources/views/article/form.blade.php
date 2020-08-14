@extends('layout')

@section('body')
<form method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($article) ? $article->id : null }}">
    <div class="card shadow">
        <div class="card-body">
            <div class="">
                <div class="form-group row">
                    <label for="title" class="col-md-2 col-form-label text-md-right">{{ __('Tiêu đề') }}</label>
                    <div class="col-md-8">
                        <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', !empty($article) ? $article->title : '') }}" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="type" class="col-md-2 col-form-label text-md-right">{{ __('Chuyên mục') }}</label>
                    <div class="col-md-8">
                        <select class="form-control" name="type" id="type">
                            @foreach(["read", "video"] as $type)
                            <option value="{{ $type }}" {{ !empty($article) && $article->type  == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="video" class="col-md-2 col-form-label text-md-right">{{ __('Link youtube') }}</label>
                    <div class="col-md-8">
                        <input id="video" type="text" class="form-control @error('video') is-invalid @enderror" name="video" value="{{ old('video', !empty($article) ? $article->video : '') }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="image" class="col-md-2 col-form-label text-md-right">{{ __('Hình ảnh') }}</label>
                    <div class="col-md-4">
                        <input name="image" type="file" id="image" />
                    </div>
                    <div class="col-md-4">
                        @if($article && $article->video)
                        <img src="{{ $article->image }}" alt="" style="height: 50px;">
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="short_content" class="col-md-2 col-form-label text-md-right font-weight-bold @error('content') is-invalid @enderror">{{ __('Giới thiệu ngắn') }}</label>
                    <div class="col-md-8">
                        <textarea required class="form-control" id="short_content" name="short_content">{!! old('short_content', !empty($article) ? $article->short_content : '') !!}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="editor" class="col-md-2 col-form-label text-md-right font-weight-bold @error('content') is-invalid @enderror">{{ __('Nội dung bài viết') }}</label>
                    <div class="col-md-8">
                        <textarea id="editor" name="content">{!! old('content', !empty($article) ? $article->content : '') !!}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button name="save" value="save" class="btn btn-{{ env('MAIN_COLOR', 'primary') }}"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
        </div>
    </div>
</form>
@endsection
@section('jscript')
@parent
<script src="/cdn/vendor/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor');
</script>
@endsection
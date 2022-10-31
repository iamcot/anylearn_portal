@extends('layout')
@section('body')
<form method="POST">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($category) ? $category->id : null }}">
    <div class="card shadow">
        <div class="card-body">
            @foreach(App\Models\I18nContent::$supports as $locale)
            <div class="form-group row">
                <label for="title{{ $locale }}" class="col-md-2 col-form-label text-md-right">{{ __('Tên thư mục') }} [{{ $locale }}]</label>
                <div class="col-md-6">
                    <input id="title{{ $locale }}" type="text" class="form-control" name="title[{{ $locale }}]" value="{{ old('title', !empty($category) ? $category->title[$locale] : '') }}" required>
                </div>
            </div>
            @endforeach
        </div>
        <div class="card-footer">
            <button name="save" value="save" class="btn btn-primary }}"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
        </div>
    </div>
</form>
@endsection

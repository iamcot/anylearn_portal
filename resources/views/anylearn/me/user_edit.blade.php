@inject('userServ', 'App\Services\UserServices')
@extends('anylearn.me.layout')
@section('spmb')
user_edit
@endsection
@section('body')
<div class="row">
    <div class="col-md-12">
        <form method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{ !empty($user) ? $user->id : null }}">
            <div class="card shadow">
                <div class="card-body">
                    <div class="">
                        <div class="form-group row">
                            <div class="container">
                                <div class="row">
                                    <div class="panel">
                                        <div class="cover-photo">
                                            @if (auth()->user()->banner != null)
                                            <div class="fb-timeline-img">
                                                <img src="{{ auth()->user()->banner }}" alt="">
                                            </div>
                                            @else
                                            <div class="fb-timeline-img">
                                                <img src="http://anylearn.vn/cdn/anylearn/img/logo-color.svg" alt="">
                                            </div>
                                            @endif
                                        </div>
                                        <div class="panel-body">
                                            @if (auth()->user()->image != null)
                                            <div class="profile-thumb">
                                                <img src="{{ auth()->user()->image }}" alt="">
                                            </div>
                                            <h2 style="margin-top:5px"><a class="text-black">{{ Auth::user()->name }}</a></h2>
                                            @else
                                            <div class="profile-thumb">
                                                <img src="http://anylearn.vn/cdn/anylearn/img/logo-color.svg" alt="">
                                            </div>
                                            <h2><a class="text-black">{{ Auth::user()->name }}</a></h2>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <h6><b>{{__('Thông tin cá nhân') }}</b></h6>
                            <label for="name" class="col-md-3 col-form-label text-md-right text-start">{{ __('Họ và tên*') }}</label>
                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', !empty($user) ? $user->name : '') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="title" class="col-md-3 col-form-label text-md-right text-start">{{ __('Ngày sinh*') }}</label>
                            <div class="col-md-8">
                                <input id="title" type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" value="{{ old('dob', !empty($user) ? $user->dob : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right text-start">{{ __('Giới tính') }}</label>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="male" name="sex" id="sex" <?php if (empty($user->sex) || $user->sex == 'male') {
                                                                                                                                echo "checked='checked'";
                                                                                                                            } ?>>
                                            <label class="form-check-label" for="male">
                                                Male
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="female" name="sex" id="sex" <?php if (!empty($user->sex) && $user->sex == 'female') {
                                                                                                                                echo "checked='checked'";
                                                                                                                            } ?>>
                                            <label class="form-check-label" for="female">
                                                Female
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="other" name="sex" id="sex" <?php if (!empty($user->sex) && $user->sex == 'other') {
                                                                                                                                echo "checked='checked'";
                                                                                                                            } ?>>
                                            <label class="form-check-label" for="other">
                                                Other
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right text-start">{{ __('CMND/CCCD') }}</label>
                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control @error('cert_id') is-invalid @enderror" name="cert_id" value="{{ old('cert_id', !empty($user) ? $user->cert_id : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right text-start">{{ __('Mã giới thiệu') }}</label>
                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control @error('refcode') is-invalid @enderror" name="refcode" value="{{ old('refcode', !empty($user) ? $user->refcode : '') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-md-3 col-form-label text-md-right text-start">{{ __('Số điện thoại') }}</label>
                            <div class="col-md-8">
                                <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', !empty($user) ? $user->phone : '') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <h6><b>{{__('Thông tin liên hệ')}}</b></h6>
                            <label for="email" class="col-md-3 col-form-label text-md-right text-start">{{ __('Email') }}</label>
                            <div class="col-md-8">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', !empty($user) ? $user->email : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="address" class="col-md-3 col-form-label text-md-right text-start">{{ __('Address') }}</label>
                            <div class="col-md-8">
                                <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address', !empty($user) ? $user->address : '') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="phone" class="col-md-3 col-form-label text-md-right text-start">{{ __('Avatar') }}</label>
                            <div class="col-md-4">
                                <input name="image" type="file" id="image" />
                            </div>
                            <div class="col-md-4">
                                <!-- @if ($user->image)
    <img src="{{ $user->image }}" alt="" style="height: 50px;">
    @endif -->
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-md-3 col-form-label text-md-right text-start">{{ __('Banner') }}</label>
                            <div class="col-md-4">
                                <input name="banner" type="file" id="image" />
                            </div>
                            <div class="col-md-4">
                                <!-- <br>
                                @if ($user->banner)
    <img src="{{ $user->banner }}" alt="" style="height: 50px;">
    @endif -->
                            </div>
                        </div>
                        {{-- <div class="form-group row">
                                <h6><b>@lang('Giới Thiệu Ngắn (Bio)')</b></h6>
                                <div class="col-md-11">
                                    <textarea class="form-control" id="introduce" name="introduce">{!! old('introduce', !empty($user) ? $user->introduce : '') !!}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <h6><b>@lang('Thông tin giới thiệu')</b></h6>
                                <div class="col-md-11">
                                    <textarea class="form-control" id="full_content" name="full_content">{!! old('full_content', !empty($user) ? $user->full_content : '') !!}</textarea>
                                </div>
                            </div> --}}
                        <ul class="nav nav-tabs" id="i18ntab" role="tablist">
                            @foreach (App\Models\I18nContent::$supports as $locale)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $locale == App\Models\I18nContent::DEFAULT ? 'active' : '' }}" id="{{ $locale }}-tab" data-toggle="tab" data-bs-toggle="tab" data-target="#{{ $locale }}box" data-bs-target="#{{ $locale }}box" type="button" role="tab" aria-controls="{{ $locale }}" aria-selected="{{ $locale == App\Models\I18nContent::DEFAULT ? 'true' : 'false' }}">{{ $locale }}</button>
                            </li>
                            @endforeach
                        </ul>
                        <div class="tab-content" id="i18ntabContent">
                            @foreach (App\Models\I18nContent::$supports as $locale)
                            <div class="p-3 tab-pane fade {{ $locale == App\Models\I18nContent::DEFAULT ? 'show active' : '' }}" id="{{ $locale }}box" role="tabpanel" aria-labelledby="{{ $locale }}-tab">
                                <div class="form-group row">
                                    <label for="introduce" class="col-md-3 col-form-label text-md-right ">{{ __('Giới Thiệu Ngắn (Bio)') }}
                                        [{{ $locale }}]</label>
                                    <div class="col-md-8">
                                        <textarea name="introduce[{{ $locale }}]" class="editor form-control">{{ old('introduce', !empty($userDT) ? $userDT['info']->introduce[$locale] : '') }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="editor{{ $locale }}" class="col-md-3 col-form-label @error('full_content') is-invalid @enderror">{{ __('Thông tin giới thiệu') }}
                                        [{{ $locale }}]</label>
                                    <div class="col-md-8">
                                        <textarea class="editor" id="editor{{ $locale }}" name="full_content[{{ $locale }}]">{{ old('full_content', !empty($userDT) ? $userDT['info']->full_content[$locale] : '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button name="save" value="save" class="btn btn-success rounded-pill shadow border-0"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@section('jscript')
@parent
<script src="/cdn/vendor/ckeditor5/ckeditor.js"></script>
<script>
    var allEditors = document.querySelectorAll('.editor');
    var editorConfig = {
        simpleUpload: {
            uploadUrl: '/upload/ckimage',
            withCredentials: true,
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
            }
        }
    };
    for (var i = 0; i < allEditors.length; ++i) {
        ClassicEditor.create(allEditors[i], editorConfig)
            .catch(error => {
                console.log(error);
            });
    }
</script>
@endsection
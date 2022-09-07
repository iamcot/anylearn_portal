@inject('userServ','App\Services\UserServices')
@extends('anylearn.me.layout')

@section('body')
<div class="row">
    <div class="col-md-9">
        <form method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id" value="{{ !empty($user) ? $user->id : null }}">
    
    <div class="card shadow">
        <div class="card-body">
            <div class="">
                <div class="form-group row">
                    <div class="row">
                        <div class="col-md-8">
                            <h6><b>Thông tin cá nhân</b></h6>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                               <a href="/me/edit"><i class="fas fa-edit"></i>@lang('Sửa thông tin')</a>
                            </div>
                        </div>

                    </div>
                    
                    <p></p>
                    <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Họ và tên*') }}</label>
                    <div class="col-md-8">
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', !empty($user) ? $user->name : '') }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="title" class="col-md-3 col-form-label text-md-right">{{ __('Ngày sinh*') }}</label>
                    <div class="col-md-8">
                        <input id="title" type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" value="{{ old('dob', !empty($user) ? $user->dob : '') }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Giới tính') }}</label>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="male" name="sex" id="sex" <?php if(empty($user->sex)||$user->sex=="male") echo"checked='checked'" ?> disabled>
                                    <label class="form-check-label" for="male">
                                        Male
                                    </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="female" name="sex" id="sex" <?php if(!empty($user->sex)&&$user->sex=="female") echo"checked='checked'"; ?> disabled>
                                    <label class="form-check-label" for="female">
                                        Female
                                    </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="other" name="sex" id="sex" <?php if(!empty($user->sex)&&$user->sex=="other") echo"checked='checked'" ?> disabled>
                                    <label class="form-check-label" for="other">
                                        Other
                                    </label>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('CMND/CCCD') }}</label>
                    <div class="col-md-8">
                        <input id="name" type="text" class="form-control @error('cert_id') is-invalid @enderror" name="cert_id" value="{{ old('cert_id', !empty($user) ? $user->cert_id : '') }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Mã giới thiệu') }}</label>
                    <div class="col-md-8">
                        <input id="name" type="text" class="form-control @error('refcode') is-invalid @enderror" name="refcode" value="{{ old('refcode', !empty($user) ? $user->refcode : '') }}" disabled>
                    </div>
                </div>     
                <div class="form-group row">
                    <label for="phone" class="col-md-3 col-form-label text-md-right">{{ __('Số điện thoại') }}</label>
                    <div class="col-md-8">
                        <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', !empty($user) ? $user->phone : '') }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                <h6><b>Thông tin liên hệ</b></h6>
                    <label for="email" class="col-md-3 col-form-label text-md-right">{{ __('Email') }}</label>
                    <div class="col-md-8">
                        <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', !empty($user) ? $user->email : '') }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address" class="col-md-3 col-form-label text-md-right">{{ __('Address') }}</label>
                    <div class="col-md-8">
                        <input id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address', !empty($user) ? $user->address : '') }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="phone" class="col-md-3 col-form-label text-md-right">{{ __('Avatar') }}</label>
                    <div class="col-md-4">
                    <input name="image" type="file" id="image" disabled />
                    </div>
                    <div class="col-md-4" >
                    <!-- @if($user->image)
                        <img src="{{ $user->image }}" alt="" style="height: 50px;">
                    @endif -->
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="phone" class="col-md-3 col-form-label text-md-right">{{ __('Banner') }}</label>
                    <div class="col-md-4">
                    <input name="banner" type="file" id="image" disabled />
                    </div>
                    <div class="col-md-4" >
                    <!-- <br>
                    @if($user->banner)
                        <img src="{{ $user->banner }}" alt="" style="height: 50px;">
                    @endif -->
                    </div>
                </div>
                <div class="form-group row">
                <h6><b>Thông tin khác</b></h6>
                    <label for="introduce" class="col-md-3 col-form-label text-md-right @error('content') is-invalid @enderror">{{ __('Giới thiệu ngắn') }}</label>
                    <div class="col-md-8">
                        <textarea class="form-control" id="introduce" name="introduce" disabled>{!! old('introduce', !empty($user) ? $user->introduce : '') !!}</textarea>
                    </div>
                </div>
                
            </div>
        </div>
        
    </div>
</form>
</div>
    <div class="col-md-3">
    <div class="card shadow">
        <div class="card-body">
            <div class="">
            <h6><b>Danh Sách Bạn Bè</b></h6>
            <div class="form-group row">
            <!-- <p class="p-2">Bạn chưa có bạn bè</p> -->
                    @foreach($userselect as $userselect)
                    @if($userselect->user_id == auth()->user()->id)
                    <div>
                    @if($userselect->image !=null)
                    <img class="avatar avatar-sm avatar-img circle" src="{{ $userselect->image }}" alt="">
                    @else
                    <img class="avatar avatar-sm avatar-img circle" src="http://anylearn.vn/cdn/anylearn/img/logo-color.svg" alt="">
                    @endif
                    <span class="mt-3 ml-2">{{ $userselect->name }}</span> 
                        <!-- <input id="name" type="text" class="form-control" name="name" value="{{ $userselect->name }}" readonly> -->
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
        
    </div>
</div>


@endsection
@section('jscript')
@parent
<script src="/cdn/vendor/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor');
</script>
@endsection
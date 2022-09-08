@inject('userServ','App\Services\UserServices')
@extends('anylearn.me.layout')
@section('body')
<nav class="navbar navbar-light bg-light d-flex justify-content-center">
<div class="row">
    <form method="POST">
                @csrf
                <div class="card shadow">
                                <div class="card-body">
                                    <div>
                                        <div class="form-group row">
                                        <div class="row">
                                    <div class="col-md-8">
                                        <h6><b>Thông tin cá nhân</b></h6>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                        <a href="" class="my-2 my-sm-0 ml-2" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fas fa-edit"></i>@lang('Sửa thông tin')</a>
                                        </div>
                                    </div>
                                </div>
                                <label for="name" class="text-start col-md-3 col-form-label text-md-right">{{ __('Họ và tên*') }}</label>
                                <div class="col-md-8">
                                    <input id="name" name="username"type="text" value="{{ !empty($userC) ? $userC->name : null }}" class="form-control @error('name') is-invalid @enderror" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="title" class="text-start col-md-3 col-form-label text-md-right">{{ __('Ngày sinh*') }}</label>
                                <div class="col-md-8">
                                    <input id="title" type="date" value="{{ !empty($userC) ? $userC->dob : null }}" class="form-control @error('dob') is-invalid @enderror" name="dob" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="text-start col-md-3 col-form-label text-md-right">{{ __('Giới tính') }}</label>
                                <div class="col-md-8 mt-2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="male" name="sex" id="sex"<?php if(empty($userC->sex)||$userC->sex=="male") echo"checked='checked'" ?> disabled >
                                                <label class="form-check-label" for="male">
                                                    Nam
                                                </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="female" name="sex" id="sex" <?php if(!empty($userC->sex)&&$userC->sex=="female") echo"checked='checked'"; ?> disabled>
                                                <label class="form-check-label" for="female">
                                                    Nữ
                                                </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                            
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <h6><b>Thông tin khác</b></h6>
                                <label for="introduce" class="text-start col-md-3 col-form-label text-md-right @error('content') is-invalid @enderror">{{ __('Giới thiệu ngắn') }}</label>
                                <br>
                                <div class="col-md-8">
                                    <textarea class="form-control" id="introduce" name="introduce" readonly>{!! old('introduce', !empty($userC) ? $userC->introduce : '') !!}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="Submit" class="btn btn-success" name="create" value="create">Tạo mới</button>
                </div> -->
    </form>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title center" id="exampleModalLabel">Tạo tài khoản con</h5>
        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
      </div>
      <div class="modal-body">
        <!-- Form  -->
        <form method="POST">
            @csrf
            <div class="card shadow">
                <div class="card-body">
                    <div class="">
                        <div class="form-group row">
                            <h6><b>Thông tin cá nhân</b></h6>
                            <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Họ và tên*') }}</label>
                            <div class="col-md-8">
                                <input id="name" value="{{ !empty($userC) ? $userC->name : null }}"  name="username"type="text" class="form-control @error('name') is-invalid @enderror" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="title" class="col-md-3 col-form-label text-md-right">{{ __('Ngày sinh*') }}</label>
                            <div class="col-md-8">
                                <input id="title" value="{{ !empty($userC) ? $userC->dob : null }}"  type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Giới tính') }}</label>
                            <div class="col-md-8 mt-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="male" name="sex" id="sex"<?php if(empty($userC->sex)||$userC->sex=="male") echo"checked='checked'" ?> >
                                            <label class="form-check-label" for="male">
                                                Nam
                                            </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="female" name="sex" id="sex"<?php if(!empty($userC->sex)&&$userC->sex=="female") echo"checked='checked'"; ?> >
                                            <label class="form-check-label" for="female">
                                                Nữ
                                            </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                        
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                        <h6><b>Thông tin khác</b></h6>
                            <label for="introduce" class="col-md-4 col-form-label text-md-right @error('content') is-invalid @enderror">{{ __('Giới thiệu ngắn') }}</label>
                            <br>
                            <div class="col-md-12">
                                <textarea class="form-control"    id="introduce" name="introduce">{!! old('introduce', !empty($userC) ? $userC->introduce : '') !!}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="Submit" class="btn btn-success" name="save" value="save">Lưu</button>
            </div>
        </form>
      </div>

    </div>
  </div>
</div>
    </div>
</div>
<h5 class="mr-2 mt-2 ml-2 mb-2"><b>Khóa Học Đã Tham Gia</b> </h5>
    <div class="col-md-12 mb-2">
        <div class="card shadow">
            <div class="card-body">
                <div class="">
                    <div class="form-group row">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card-img">
                                    <div class="imagebox">
                                    <img src="https://s3-ap-southeast-1.amazonaws.com/anylearns2/items/714/HRHsMSrMSH1662562998.jpg" class="img-fluid">
                                    </div>
                                    <div class="class-title mt-1 fw-bold p-1 text-success"> Cách ba mẹ truyền cảm hứng học tập cho con</div>                          
                                </div>
                                
                            </div>
                            <div class="col-md-3">
                                <div class="card-img">
                                    <div class="imagebox">
                                    <img src="https://s3-ap-southeast-1.amazonaws.com/anylearns2/items/714/HRHsMSrMSH1662562998.jpg" class="img-fluid">
                                    </div>
                                    <div class="class-title mt-1 fw-bold p-1 text-success"> Cách ba mẹ truyền cảm hứng học tập cho con</div>                          
                                </div>
                                
                            </div>
                            <div class="col-md-3">
                                <div class="card-img">
                                    <div class="imagebox">
                                    <img src="https://s3-ap-southeast-1.amazonaws.com/anylearns2/items/714/HRHsMSrMSH1662562998.jpg" class="img-fluid">
                                    </div>
                                    <div class="class-title mt-1 fw-bold p-1 text-success"> Cách ba mẹ truyền cảm hứng học tập cho con</div>                          
                                </div>
                                
                            </div>
                            <div class="col-md-3">
                                <div class="card-img">
                                    <div class="imagebox">
                                    <img src="https://s3-ap-southeast-1.amazonaws.com/anylearns2/items/714/HRHsMSrMSH1662562998.jpg" class="img-fluid">
                                    </div>
                                    <div class="class-title mt-1 fw-bold p-1 text-success"> Cách ba mẹ truyền cảm hứng học tập cho con</div>                          
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div>
</nav>

@endsection
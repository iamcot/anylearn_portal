@inject('userServ','App\Services\UserServices')
@extends('anylearn.me.layout')

@section('body')
<nav class="navbar navbar-light bg-light d-flex justify-content-center">
<div class="row mt-2">
    <form>
        <div class="input-group">
            <input name="inputsearch" value="{{ !empty($search) ? $search : null }}"  class="form-control py-2 border-right-0 border" type="text">
            <span class="input-group-append">
                <button class="btn btn-outline-secondary border-left-0 border " type="submit" name="search" value="search"> 
                    <i class="fa fa-search"></i>
                </button>
                <button class="btn btn-secondary my-2 my-sm-0 ml-2" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">Tạo Tài khoản</button>
              </span>
        </div>
    </form>
</div>
</nav>
@foreach($childuser as $childuser)
    <nav class="navbar navbar-light bg-light d-flex justify-content-center">
    <form class="form-inline center p-1 mb-2">
        @if($childuser->sex =='female')
        <i class="fas fa-female fa-2x mr-2"></i>
        @else
        <i class="fas fa-male fa-2x mr-2"></i>
        @endif
        <input class="form-control mr-sm-2" type="hidden" name="childid" value="{{ $childuser->id }}" readonly>
        <!-- <button style="button" class="btn-hidden" id="{{ $childuser->id }}" name="childedit" value="childedit"></button> -->
        <input class="form-control mr-sm-2" type="text" value="{{ $childuser->name }}" onclick="edit({{ $childuser->id }});" readonly>
        <button class="btn btn-secondary my-2 my-sm-0 ml-3" name="delete" value="delete" type="submit">Xóa tài khoản</button>
        <form method="get">
        <button style="button" class="btn-hidden" id="{{ $childuser->id }}" name="childedit" value="childedit"></button>
        </form>
    </form>
    </nav>
@endforeach

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
                                <input id="name" name="username"type="text" class="form-control @error('name') is-invalid @enderror" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="title" class="col-md-3 col-form-label text-md-right">{{ __('Ngày sinh*') }}</label>
                            <div class="col-md-8">
                                <input id="title" type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right">{{ __('Giới tính') }}</label>
                            <div class="col-md-8 mt-2">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="male" name="sex" id="sex" >
                                            <label class="form-check-label" for="male">
                                                Nam
                                            </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="female" name="sex" id="sex">
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
                                <textarea class="form-control" id="introduce" name="introduce">{!! old('introduce', !empty($user) ? $user->introduce : '') !!}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="Submit" class="btn btn-success" name="create" value="create">Tạo mới</button>
            </div>
        </form>
      </div>

    </div>
  </div>
</div>
@endsection
<script>
    function edit($id) {
        console.log($id);
        document.getElementById($id).click();
    }
</script>
@inject('userServ','App\Services\UserServices')
@extends('anylearn.me.layout')

@section('body')
<div class="row mb-2 text-end">
    <form>
        <button class="btn btn-secondary my-2 my-sm-0 ml-2 " type="button" data-bs-toggle="modal" data-bs-target="#exampleModal">Tạo Tài khoản</button>
    </form>
</div>

    <div class="card shadow">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover">
            <thead class="table-secondary text-secondary">
                <tr>
                    <th class="text-center border-0" width="25%" scope="col">Họ Tên</th>
                    <th class="border-0">Giới Tính</th>
                    <th class="text-center border-0">Ngày Sinh</th>
                    <th width="15%" class="text-right border-0" scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            
                @foreach($childuser as $row)
                    <tr>
                        <form action="" method="get">
                        <input class="form-control mr-sm-2" type="hidden" name="childid" value="{{ $row->id }}" readonly>
                            <td>{{ $row->name }}</td>
                            @if($row->sex =='female')
                            <td class="">Nữ</td>
                            @else
                            <td class="">Nam</td>
                            @endif
                            <td class="text-center">{{ ($row->dob) }}</td>
                            <td class="text-right"> <button style="submit" class="btn btn-success" id="{{ $row->id }}" name="childedit" value="{{ $row->id }}" >Chi tiết</button></td>
                        </form>
                    </tr>
                @endforeach
            
            </tbody>
        </table>

    </div>
</div>


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
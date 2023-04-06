@extends('anylearn.me.layout')
@section('spmb')
    admitstudent
@endsection
@section('body')

    <div class="container">
        <h1 class="text-center">Tiếp nhận học sinh</h1>
        <form>
            <div class="input-group mb-3 mt-3">
                <input type="text" class="form-control" name="id" placeholder="Nhập mã nhập học"
                    aria-label="Nhập mã nhập học" aria-describedby="button-addon2" />
                <button class="btn btn-outline-primary" name="check" value="check" id="button-addon2"
                    data-mdb-ripple-color="dark">
                    Xác nhận
                </button>
            </div>
        </form>
        @if ($data ==null)
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="{{ isset($data->iimage) ? $data->iimage : null }}" class="card-img-top" alt="..." />
                                </div>
                                <div class="col-md-8">
                                    <h5 class="card-title">{{ $data->title }}</h5>
                                    <p class="card-text">
                                        {{ $data->short_content}}
                                    </p>
                                    @if ($data->confirm_count == 0)
                                    <a href="{{ route('class.author.confirmjoin' , ['itemId' =>  $data->itemId ]) }}?join_user={{ $data->userId }}" class="btn btn-success btn-sm button-0 end-0 float-right">Tiếp nhận học viên</a>
                                    @else
                                    <a class="btn btn-outline-success position-absolute bottom-0 end-0" disabled>Đã tiếp nhận</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
    @if ($data == null)
    @else
        <!-- Profile Header -->
        <div class="container mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @if ($data->uimage !=null)
                        <div class="col-md-2">
                            <img src="{{$data->uimage}}" alt="Profile Picture" class="img-fluid">
                        </div>
                        @endif
                        <div class="col-md-10">
                            <h1 class="fw-bold mb-0">{{ $data->name}}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <section class="profile-content mt-3">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Thông tin giới thiệu</h5>
                                <p class="card-text">{{ $data->introduce }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

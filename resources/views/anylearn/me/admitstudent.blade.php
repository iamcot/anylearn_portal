@extends('anylearn.me.layout')
@section('spmb')
    admitstudent
@endsection
@section('body')
    {{-- <div class="row">
    <div class="col-12 p-5">
        <h4>@lang('Học viên đã đăng ký')
            <!-- <button class="btn btn-sm btn-primary float-right" name="tab" value="teachers"><i class="fas fa-save"></i> @lang('Lưu thay đổi')</button> -->
        </h4>
        @if (!empty($students) && count($students) > 0)
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Tên học viên</th>
                    <th>Ngày đăng ký</th>
                    <th>Xác nhận (buổi)</th>
                    <th>Chứng chỉ</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            @foreach ($students as $student)
            <tr>
                <td><input type="checkbox" name="student_ids[{{ $student->id }}]" id=""></td>
                <td>{{ $student->name }}</td>
                <td>{{ date('d/m/Y', strtotime($student->created_at)) }}</td>
                <td>{{ $student->confirm_count }}</td>
                <td>
                    @if ($student->cert)
                    <a href="{{ $student->cert }}" target="_blank"><i class="fa fa-certificate"></i></a>
                    @endif
                </td>
                <td>
                    @if ($student->confirm_count == 0)
                    <a href="{{ route('class.author.confirmjoin' , ['itemId' =>  $course['info']->id ]) }}?join_user={{ $student->id }}" class="btn btn-success btn-sm">Xác nhận tham gia</a>
                    @endif
                    <a href="{{ route('class.author.cert' , ['itemId' =>  $course['info']->id, 'userId' => $student->id]) }}" class="btn btn-warning btn-sm mt-1">Cấp chứng chỉ</a>
                </td>
            </tr>
            @endforeach
        </table>
        @else
        <p>@lang('Chưa có học viên nào đăng ký')</p>
        @endif
    </div>
</div> --}}
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
                                    <a href="#" class="btn btn-outline-success position-absolute bottom-0 end-0">Tiếp nhận học viên</a>
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
                        <div class="col-md-2">
                            <img src="{{$data->uimage}}" alt="Profile Picture" class="img-fluid">
                        </div>
                        <div class="col-md-10">
                            <h1 class="fw-bold mb-0">{{ $data->name}}</h1>
                            {{-- <p class="lead mb-0">Software Engineer</p> --}}
                            <p class="mb-0">{{ $data->address}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <section class="profile-content mt-3">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Thông tin giới thiệu</h5>
                                <p class="card-text">{{ $data->introduce }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Thông tin Khác</h5>
                                <ul class="list-group">
                                    <li class="list-group-item">{{ $data->phone}}</li>
                                    <li class="list-group-item">{{ $data->email}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

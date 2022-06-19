@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.layout')
@section('title')
Các Chuyên Gia & Giảng Viên Hàng Đầu - anyLEARN
@endsection
@section('description')
Các chuyên gia & giảng viên tại anyLEARN là những cá nhân thành công trong công việc và ngành nghề của họ. Tại anyLEARN, họ mong muốn dùng kinh nghiệm của mình để truyền đạt và tạo ra giá trị cho học viên.@endsection
@section('body')
<div class="row">
    <div class="col-md-9">
        @include('anylearn.widget.breadcrumb', ['breadcrumb' => $breadcrumb])
        @if($hasSearch && $searchNotFound)
        <p>Không tìm thấy chuyên viên bạn đang tìm kiếm, hãy tìm thử các chuyên viên dưới đây nhé.</p>
        <hr>
        @endif
        @if(count($list) <= 0) <p>
            </p>
            @else

            <ul class="teacher_list row list-unstyled grid">
                @foreach($list as $school)
                <li class="col-xs-6 col-md-4 mb-5 d-flex ">
                    <div class="card shadow align-self-stretch vw-100">
                        <div class="card-body p-2">
                            <div class="imagebox">
                                <img class="img-fluid" src="{{ $school->image ?? '/cdn/img/school-no-image.png' }}">
                            </div>
                            <div class="description">
                                <h3 class="fw-bold">{{ $school->name }}</h3>
                                <div>
                                    <ul class="list-unstyled list-inline">
                                        @foreach($school->categories as $category)
                                        <li class="list-inline-item border border-success rounded text-success p-1 small mt-1">{{ $category->title }}</li>
                                        @endforeach
                                    </ul>
                                </div>

                            </div>

                        </div>
                        <div class="align-self-center m-2">
                            <a href="{{ route('classes', ['role' => 'school', 'id' => $school->id ]) }}" class="fw-bold btn border-0 rounded-pill btn-success">KHOÁ HỌC</a>
                        </div>

                    </div>
                </li>
                @endforeach
            </ul>
            {{ $listPaginate }}
            @endif
    </div>
    <div class="col-md-3">
        <form action="" method="get" id="schoolsearch">
            <div class="card shadow">
                <div class="card-body">
                    <h6>BỘ LỌC</h6>
                    <div class="form-group mb-2">
                        <select class="form-control location-tree rounded-pill text-secondary" data-next-level="district" name="p">
                            <option value="">Tỉnh/Thành Phố</option>
                            @foreach($provinces as $province)
                            <option value="{{ $province->code }}" {{ !empty($location) && $province->code == $location->province_code ? "selected" : ""}}>{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <select class="form-control location-tree rounded-pill text-secondary" id="select-district" name="d">
                            @if(empty($wards))
                            <option value="">Quận/Huyện</option>
                            @else
                            @foreach($wards as $ward)
                            <option value="{{ $ward->code }}" {{ $ward->code == $location->ward_code ? "selected" : ""}}>{{ $ward->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <select class="form-control location-tree rounded-pill text-secondary" id="select-type" name="t">
                            <option value="">Hình thức</option>
                            <option value="online" {{ request()->get('t') == 'online' ? 'selected' : '' }}>Học trực tuyến</option>
                            <option value="offline" {{ request()->get('t') == 'offline' ? 'selected' : '' }}>Học tại trung tâm</option>
                        </select>
                    </div>
                    <div class="form-group mb-2">
                        <select class="form-control location-tree rounded-pill text-secondary" id="select-category" name="c">
                            <option value="">Lĩnh vực</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request()->get('c') == $cat->id ? 'selected' : '' }}>{{ $cat->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr>
                    <button id="searchbtn" name="a" value="search" class="btn border-0 rounded-pill btn-success ">@lang('TÌM KIẾM')</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
@section('jscript')
@parent
<script src="/cdn/js/location-tree.js"></script>
@endsection
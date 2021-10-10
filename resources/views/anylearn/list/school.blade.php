@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.layout')
@section('body')
@include('anylearn.widget.breadcrumb', ['breadcrumb' => $breadcrumb])
<div class="row">
    <div class="col-md-8">
        @if($hasSearch && $searchNotFound)
        <p>Không tìm thấy trung tâm bạn đang tìm kiếm, hãy tìm thử các trung tâm dưới đây nhé.</p>
        <hr>
        @endif
        @if(count($list) <= 0) <p>
            </p>
            @else
            <ul class="row list-unstyled grid">
                @foreach($list as $school)
                <li class="col-xs-6 col-md-4 mb-5">
                    <div class="card shadow p-1">
                        <div class="card-body">
                            <div class="imagebox">
                                <img class="img-fluid" src="{{ $school->image ?? '/cdn/img/school-no-image.png' }}">
                            </div>
                            <div class="description">
                                <h6 class="fw-bold">{{ $school->name }}</h6>
                                <div class="text-center">
                                    <a href="{{ route('classes', ['role' => 'school', 'id' => $school->id ]) }}" class="btn border-0 rounded-pill btn-success">KHOÁ HỌC <i class="fa fa-chevron-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            {{ $list->appends(request()->query())->links() }}
            @endif
    </div>
    <div class="col-md-4">
        <form action="" method="get" id="schoolsearch">
            <div class="card shadow">
                <div class="card-body">
                    <h5>BỘ LỌC</h5>
                    <div class="form-group mb-2">
                        <select class="form-control location-tree rounded-pill text-secondary" data-next-level="district" name="p">
                            <option value="">--Tỉnh/Thành Phố--</option>
                            @foreach($provinces as $province)
                            <option value="{{ $province->code }}" {{ !empty($location) && $province->code == $location->province_code ? "selected" : ""}}>{{ $province->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control location-tree rounded-pill text-secondary" id="select-district" name="d">
                            @if(empty($wards))
                            <option value="">--Quận/Huyện--</option>
                            @else
                            @foreach($wards as $ward)
                            <option value="{{ $ward->code }}" {{ $ward->code == $location->ward_code ? "selected" : ""}}>{{ $ward->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <hr>
                    <button id="searchbtn" name="a" value="search" class="btn border-0 rounded-pill btn-success ">@lang('TÌM KIÉM')</button>
                </div>
            </div>
    </div>
</div>

@endsection
@section('jscript')
@parent
<script src="/cdn/js/location-tree.js"></script>
@endsection
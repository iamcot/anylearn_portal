@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.layout')
@section('body')
@include('anylearn.widget.breadcrumb', ['breadcrumb' => $breadcrumb])
@if(!empty($author))
<div class="card shadow mb-5">
    <div class="card-body">

        <div class="row p-3">
            <div class="col-md-3">
                @if($author->image)
                <div class="imagebox p-0">
                    <img src="{{ $author->image }}" class="img-fluid" alt="">
                </div>
                @endif

                <h5 class="pt-2 fw-bold">{{ $author->name }}</h5>
            </div>
            <div class="col-md-9 text-secondary">
                <div class="collapse-module">
                    <div class="collapse" id="introduceCollapse">
                        <p>{{ $author->introduce }}</p>
                        @if($author->role == 'school')
                        <p>Người đại diện: {{ $author->title }}</p>
                        @endif
                        <div>{!! $author->full_content !!}</div>
                    </div>
                    @if($author->full_content && strlen($author->full_content) > 200)
                    <div class="text-center">
                        <button class="ps-4 pe-4 border-0 btn btn-white rounded-pill shadow fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#introduceCollapse" aria-expanded="false" aria-controls="introduceCollapse"></button>
                    </div>
                    @endif
                </div>

            </div>

        </div>

    </div>
</div>
@endif
<div class="row mb-2">
    <div class="col-md-9 grid-box">
    @if($hasSearch && $searchNotFound)
        <p>Không tìm thấy khoá học bạn đang tìm kiếm, hãy tìm thử các khoá học dưới đây nhé.</p>
        <hr>
        @endif
        @if(count($classes) <= 0)  <p>Không tìm thấy khoá học nào.</p>
            @else
            <ul class="row list-unstyled grid">
                @foreach($classes as $class)
                <li class="col-xs-6 col-md-4 mb-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-img">
                            <div class="imagebox">
                                <img src="{{ $class->image }}" class="img-fluid">
                            </div>
                            <div class="class-title mt-1 fw-bold p-1">@if($class->is_hot) <span class="badge bg-danger "><i class="fas fa-fire"></i> HOT</span> @endif {{ $class->title }}</div>
                            <div class="p-1">
                                @if($class->org_price > 0)
                                <span class="bg-success badge mr-1">-{{ number_format((($class->org_price - $class->price) / $class->org_price) * 100, 0,".",",") }}%</span>
                                <span class="text-secondary text-decoration-line-through mr-1">{{ number_format($class->org_price, 0, ',', '.') }}</span>
                                @endif
                                <span class="text-success fw-bold">{{ number_format($class->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="p-1">@include('anylearn.widget.rating', ['score' => $class->rating ?? 0])</div>
                            <div class="text-center mb-2">
                                <a href="{{ $itemServ->classUrl($class->id) }}" class="btn btn-success rounded-pill border-0 w-75">CHI TIẾT</a>
                            </div>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            {{ $classes->appends(request()->query())->links() }}
            @endif
    </div>
    <div class="col-md-3">
        <div class="card shadow">
            <div class="card-body">
                <form action="" method="get" id="schoolsearch">
                    <h6>BỘ LỌC</h6>
                    <label for="customRange1" class="form-label">Giá</label>
                    <input name="price" type="range" class="form-range" min="0" max="10000000" step="10000" id="priceRange" value="{{ request()->get('price') ?? 1000000 }}" oninput="onPriceChange(this)">
                    <div>
                        0 - <span id="priceRangeShow">{{ number_format(request()->get('price') ?? 1000000) }}</span>
                    </div>
                    <div class="form-group mb-2 mt-2">
                        <select class="form-control location-tree rounded-pill text-secondary" id="select-type" name="t">
                            <option value="">Hình thức</option>
                            <option value="online" {{ request()->get('t') == 'online' ? 'selected' : '' }}>Học trực tuyến</option>
                            <option value="offline" {{ request()->get('t') == 'offline' ? 'selected' : '' }}>Học tại trung tâm</option>
                            <option value="digital" {{ request()->get('t') == 'digital' ? 'selected' : '' }}>Mã Ứng dụng</option>
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
                    <button id="searchbtn" name="a" value="search" class="btn border-0 rounded-pill btn-success ">@lang('TÌM KIÉM')</button>

                </form>

            </div>
        </div>
    </div>
</div>

@endsection
@section('jscript')
@parent
<script>
    function onPriceChange(input) {
        console.log(input.value);
        $('#priceRangeShow').text(new Intl.NumberFormat('vn-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(input.value));
    }
</script>
@endsection
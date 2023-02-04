@inject('itemServ', 'App\Services\ItemServices')
@inject('videoServ', 'App\Services\VideoServices')
@extends('anylearn.layout')
@section('title')
{{ $item->title }}
@endsection
@section('spmb')
pdp
@endsection
@section('canonical')
{{ $itemServ->classUrl($item->id) }}
@endsection
@section('body')
<section class="container mt-5" id="spmc" data-spm="{{ $item->id }}">
    <div class="card shadow border-0">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-md-6 imagebox">
                    <img class="img-fluid rounded" src="{{ $item->image }}" />
                </div>
                <div class="col-lg-8 col-md-6 text-secondary">
                    @include('anylearn.widget.breadcrumb', ['breadcrumb' => $breadcrumb])
                    <h2 class="text-dark fw-bold">[{{ $item->subtype }}] {{ $item->title }}</h2>
                    <p class=""><i class=" text-success fa fa-{{ $author->role == 'teacher' ? 'user' : 'university' }}"></i> <a href="{{ route('classes', [
                                    'role' => $author->role,
                                    'id' => $author->id,
                                ]) }}" class="text-decoration-none text-success">{{ $author->name }}</a></p>
                    <div>
                        <ul class="list-unstyled list-inline">
                            @foreach ($categories as $category)
                            <li class="list-inline-item border border-success rounded text-success p-1 fw-light">
                                {{ $category->title }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        @include('anylearn.widget.rating', ['score' => $item->rating ?? 0])
                    </div>
                    @if($item->ages_min > 0 && $item->ages_max > 0) <p>@lang('Độ tuổi'): {{ $item->ages_min . '-' . $item->ages_max }}</p>@endif
                    @if($item->seats > 0) <p>@lang('Số lượng'): {{ $item->seats }}</p> @endif
                    <p><i class="text-success fa fa-calendar"></i> @lang('Khai giảng:')
                        {{ date('d/m/Y', strtotime($item->date_start)) }}
                        {{ $num_schedule <= 1 ? '' : '(có ' . $num_schedule . ' buổi học)' }}
                    </p>
                    <p>{!! $item->short_content !!}</p>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-6 col-md-6">
                        <h3>
                            @if ($item->org_price > 0)
                            <span class="bg-success badge mr-1">-{{ number_format((($item->org_price - $item->price) / $item->org_price) * 100, 0, '.', ',') }}%</span>
                            <span class="text-secondary text-decoration-line-through mr-1">{{ number_format($item->org_price, 0, ',', '.') }}đ</span>
                            @endif
                            <span class="text-success fw-bold">{{ number_format($item->price, 0, ',', '.') }}đ</span>

                        </h3>

                    </div>
                    <div class="col-lg-6 col-md-6 d-flex">
                        <div class="flex-fill pt-2">
                            @if (!auth()->check())
                            <a class="border-0 btn btn-success form-control rounded-pill" href="{{ route('login') . '?cb=' . urlencode($itemServ->classUrl($item->id)) }}">@lang('Đăng ký học')</a>
                            @else
                            <form action="{{ route('add2cart') }}" method="get" id="pdpAdd2Cart">
                                <input type="hidden" name="class" value="{{ $item->id }}">
                                <button name="action" value="add2cart" class="border-0 btn btn-success form-control rounded-pill">@lang('Đăng ký học')</button>
                            </form>
                            @endif
                        </div>
                        @if ($is_fav)
                        <div class="flex-end p-2"><a class=" text-danger" href="{{ route('class.like', ['itemId' => $item->id]) }}"><i class="fas fa-2x fa-heart"></i></a></div>
                        @else
                        <div class="flex-end p-2 text-success"><a class="text-danger" href="{{ route('class.like', ['itemId' => $item->id]) }}"><i class="far fa-2x fa-heart"></i></a></div>
                        @endif
                        <div class="flex-end p-2 text-success"><a><i class="far fa-2x fa-share-square"></i></a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="mt-4">
        <ul class="nav nav-tabs" id="pdptab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link text-secondary fw-bold @if(!$videoServ->checkOrder($item->id)) active @endif" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab" aria-controls="content" aria-selected="true">@lang('MÔ TẢ')</button>
            </li>
            @if ($author->role == 'school')
            <!-- <li class="nav-item" role="presentation">
                    <button class="nav-link text-secondary fw-bold" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button" role="tab" aria-controls="teachers" aria-selected="false">GIẢNG VIÊN</button>
                </li> -->
            @endif
            @if ($item->subtype == 'video')
            <li class="nav-item" role="presentation">
                <button class="nav-link text-secondary fw-bold @if($videoServ->checkOrder($item->id)) active @endif" id="video-tab" data-bs-toggle="tab" data-bs-target="#video" type="button" role="tab" aria-controls="video" aria-selected="false">@lang('BÀI HỌC')</button>
            </li>
            @endif
            @if (count($reviews) > 0)
            <li class="nav-item" role="presentation">
                <button class="nav-link text-secondary fw-bold" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab" aria-controls="review" aria-selected="false">@lang('ĐÁNH GIÁ')</button>
            </li>
            @elseif(Auth::check())
            @if ((auth()->user()->role == 'school') | (auth()->user()->role == 'teacher'))
            <li class="nav-item" role="presentation">
                <button class="nav-link text-secondary fw-bold" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab" aria-controls="review" aria-selected="false">@lang('ĐÁNH GIÁ')</button>
            </li>
            @endif
            @endif
        </ul>
        <div class="tab-content border border-top-0 mb-5 shadow" id="myTabContent">
            <div class="tab-pane fade show @if(!$videoServ->checkOrder($item->id)) active @endif p-2" id="content" role="tabpanel" aria-labelledby="content-tab">
                @if (\App::getLocale() == 'vi')
                <div class="collapse-module pb-4">
                    <div class="collapse" id="contentCollapse">
                        {!! $item->content !!}
                    </div>
                    <div class="text-center">
                        <button class="ps-4 pe-4 border-0 btn btn-white rounded-pill shadow fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#contentCollapse" aria-expanded="false" aria-controls="contentCollapse">
                    </div>
                </div>
                @else
                <div class="collapse-module-en pb-4">
                    <div class="collapse" id="contentCollapse">
                        {!! $item->content !!}
                    </div>
                    <div class="text-center">
                        <button class="ps-4 pe-4 border-0 btn btn-white rounded-pill shadow fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#contentCollapse" aria-expanded="false" aria-controls="contentCollapse">
                    </div>
                </div>
                @endif

            </div>
            @if ($author->role == 'school')
            <!-- <div class="tab-pane fade p-2" id="teachers" role="tabpanel" aria-labelledby="teachers-tab">...</div> -->
            @endif
            @if (1==1)
            <div class="tab-pane fade @if($videoServ->checkOrder($item->id)) show active @endif" id="video" role="tabpanel" aria-labelledby="video-tab">
                @include('anylearn.pdp.video')
            </div>
            @endif
            @if (count($reviews) > 0)
            <div class="tab-pane fade ps-4 pe-4" id="review" role="tabpanel" aria-labelledby="review-tab">
                @include('anylearn.pdp.review')
            </div>
            @elseif(Auth::check())
            @if ((auth()->user()->role == 'school') || (auth()->user()->role == 'teacher'))
            <div class="tab-pane fade ps-4 pe-4" id="review" role="tabpanel" aria-labelledby="review-tab">
                @include('anylearn.pdp.review')
            </div>
            @endif
            @endif
        </div>
    </div>
    @if (\App::getLocale() == 'vi')
    <div class="mb-3">
        @include('anylearn.home.classes', [
        'title' => 'KHOÁ HỌC LIÊN QUAN',
        'carouselId' => 'pdp-classes',
        'data' => $hotItems['list'],
        ])
    </div>
    @else
    <div class="mb-3">
        @include('anylearn.home.classes', [
        'title' => 'RELATED COURSES',
        'carouselId' => 'pdp-classes',
        'data' => $hotItems['list'],
        ])
    </div>
    @endif
</section>
@endsection
@section('jscript')
@parent
<script>
    $('.carousel4 .owl-carousel').owlCarousel({
        margin: 10,
        nav: true,
        navText: [
            '<span class="owl-carousel-control-icon rounded-circle border p-2 bg-white shadow"><i class="fas fa-2x fa-angle-left text-secondary"></i></span>',
            '<span class="owl-carousel-control-icon-right rounded-circle border  p-2 bg-white shadow"><i class="fas fa-2x fa-angle-right text-secondary"></i></span>'
        ],
        responsive: {
            0: {
                items: 2
            },
            600: {
                items: 5
            }
        }
    });


    $("#pdpAdd2Cart").on("submit", function(event) {
        event.preventDefault();
        gtag("event", "add_to_cart", {
            "items": [{
                "id": "{{ $item->id }}",
                "name": "{{ $item->title }}",
                "price": "{{ $item->price }}",
                "quantity": 1,
                "currency": "VND"
            }]
        });
        $(this).unbind('submit').submit();
    });
</script>
@endsection
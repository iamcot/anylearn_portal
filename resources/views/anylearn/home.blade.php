@extends('anylearn.layout')
@section('title')
anyLEARN - HỌC không giới hạn
@endsection
@section('body')
<section id="banners">
    <img class="img-fluid" src="/cdn/anylearn/img/banner_1.svg" alt="">
</section>
<section class="text-center">
    <h5 class="mt-3 fw-bold text-secondary">
        Nền tảng tìm kiếm Trường học và Chuyên gia hàng đầu, Khóa học Offline và Online
    </h5>
    <div class="mt-3 text-center" id="search">
        <form action="/classes" method="get" id="schoolsearch">
            <button class="border-0 bg-white" name="a" value="search"><i class="fa fa-search text-success"></i></button>
            <input type="text" name="s" class="form-control rounded-pill shadow" placeholder="Tìm khoá học...">
        </form>
    </div>
    <div id="quote" class="mt-3">
        <img id="quote-top" src="/cdn/anylearn/img/quote-top.svg" class="img-fluid" alt="">
        <img src="/cdn/anylearn/img/quote.png" class="img-fluid" alt="">
        <span class="quote-text">{{ $quote['quoteText'] }}<br>- {{ $quote['quoteAuthor'] }}</span>
        <img id="quote-bottom" src="/cdn/anylearn/img/quote-bottom.svg" class="img-fluid" alt="">
    </div>
</section>
@include('anylearn.home.promotions', [
'title' => 'ƯU ĐÃI HOT',
'carouselId' => 'promotions'
])
@foreach($classes as $classBlock)
@include('anylearn.home.classes', [
'title' => $classBlock['title'],
'carouselId' => 'class_' . $loop->index,
'data' => $classBlock['classes']
])
@if(count($classes) == 1 || $loop->index == floor(count($classes) / 2) - 1 )
@include('anylearn.home.articles', [
'title' => 'SỰ KIỆN SẮP DIỄN RA',
'carouselId' => 'events',
'data' => $events
])
@endif
@endforeach


@include('anylearn.home.articles', [
'title' => 'HỌC VÀ HỎI',
'carouselId' => 'asks',
'data' => $articles
])
<section class="hot-items mt-5">
</section>
@endsection
@section('jscript')
<script>
    $(document).ready(function() {
        $('.carousel3 .owl-carousel').owlCarousel({
            margin: 10,
            nav:true,
            navText: [
                '<span class="owl-carousel-control-icon rounded-circle border p-2 bg-white shadow"><i class="fas fa-2x fa-angle-left text-secondary"></i></span>',
                '<span class="owl-carousel-control-icon-right rounded-circle border  p-2 bg-white shadow"><i class="fas fa-2x fa-angle-right text-secondary"></i></span>'
            ],
            responsive: {
                0: {
                    items: 1
                },
                600: {
                    items: 3
                }
            }
        });
        $('.carousel4 .owl-carousel').owlCarousel({
            margin: 10,
            nav:true,
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
    });
</script>
@endsection
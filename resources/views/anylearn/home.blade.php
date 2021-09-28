@extends('anylearn.layout')
@section('title')
anyLEARN - HỌC không giới hạn
@endsection
@section('body')
<section id="banners">
    <img class="img-fluid" src="/cdn/anylearn/img/banner_1.svg" alt="">
</section>
<section class="text-center">
    <h1 class="mt-3 fw-bold">
        Chào mừng bạn đến với <span class="text-success">anyLEARN</span>
    </h1>
    <div class="mt-3 text-center" id="search">
        <i class="fa fa-search text-success"></i>
        <input type="text" class="form-control rounded-pill shadow" placeholder="Hôm nay bạn muốn học gì ?">
    </div>
    <div id="quote" class="mt-3">
        <img id="quote-top" src="/cdn/anylearn/img/quote-top.svg" class="img-fluid" alt="">
        <img src="/cdn/anylearn/img/quote.png" class="img-fluid" alt="">
        <span class="quote-text">Học tập là một hành trình không có giới hạn, mỗi hạt giống tri thức bạn gieo xuống hôm nay, sẽ cho bạn lương thực nuôi sống tâm hồn và trí tuệ ngày mai.<br>- Hoài Trinh</span>
        <img id="quote-bottom" src="/cdn/anylearn/img/quote-bottom.svg" class="img-fluid" alt="">
    </div>
</section>
@include('anylearn.home.promotions', [
    'title' => 'ƯU ĐÃI HOT',
    'carouselId' => 'promotions'
    ])
    @include('anylearn.home.classes', [
    'title' => 'KHOÁ HỌC ÂM NHẠC', 
    'carouselId' => 'events'
    ])
@include('anylearn.home.articles', [
    'title' => 'HỌC VÀ HỎI', 
    'carouselId' => 'asks'
    ])
<section class="hot-items mt-5">
</section>
    @endsection
    @section('jscript')
    @endsection
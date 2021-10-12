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
    @endsection
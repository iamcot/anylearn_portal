@inject('userServ','App\Services\UserServices')
@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.layout')

@section('body')
@include('anylearn.widget.breadcrumb', ['breadcrumb' => $breadcrumb])
<div class="mb-5">
    <section class="text-center">
        <h5 class="mt-3 fw-bold text-secondary">
            Xin chào, anyLEARN giúp gì được cho bạn?
        </h5>
        <div class="mt-3 text-center" id="search">
            <form action="/helpcenter/s" method="get" id="helpcenterSearch">
                <button class="border-0 bg-white" name="a" value="search"><i class="fa fa-search text-success"></i></button>
                <input type="text" name="s" class="form-control rounded-pill shadow" placeholder="Làm sao để mua hàng, Làm sao để thanh toán...">
            </form>
        </div>
    </section>
    <section class="mt-5">
        <h5>Các câu hỏi được quan tâm</h5>
        <ul  class="row">
            @foreach($topKnowledge as $knowledge)
            <li class="col-xs-12 col-md-6"><a class=" text-black"  href="{{ route('helpcenter.knowledge', ['id' => $knowledge->id, 'url' => $knowledge->url ]) }}">{{  $knowledge->title }}</a></li>
            @endforeach
        </ul>
    </section>
    <section class="mt-5">
        <h5>Các chủ đề</h5>
        <div class="row">
            @foreach($topics as $topic)
            <div class="col-xs-6 col-md-3">
                <div class="border rounded-3 p-5 text-center">
                    <a class="fw-bold text-success text-decoration-none" href="{{ route('helpcenter.topic', ['url' => $topic->url]) }}">{{ $topic->title }}</a>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    <!-- <section class="text-center mt-5">
        <h5 class="mt-3 fw-bold text-secondary">
            Bạn vẫn cần trợ giúp ?
        </h5>
        <p>anyLEARN sẽ có đội ngũ hỗ trợ bạn mọi lúc</p>
        <button class="btn btn-success border-0 rounded-pill" >Trò chuyện ngay</button>
    </section> -->
</div>
@endsection
@section('jscript')
@parent
<script src='/cdn/anylearn/chatbot/js/widget.js'></script>
@endsection
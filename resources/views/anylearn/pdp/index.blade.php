@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.layout')
@section('title')
{{ $item->title }}
@endsection
@section('body')
<div class="container mt-5">
    <div class="card shadow border-0">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-md-6 imagebox">
                    <img class="img-fluid rounded" src="{{ $item->image }}" />
                </div>
                <div class="col-lg-8 col-md-6 text-secondary">
                    @include('anylearn.widget.breadcrumb', ['breadcrumb' => $breadcrumb])
                    <h2 class="text-dark fw-bold">{{ $item->title }}</h2>
                    <p class="">{{ $author->name }}</p>
                    <div>
                        <ul class="list-unstyled list-inline">
                            @foreach($categories as $category)
                            <li class="list-inline-item border border-success rounded text-success p-1 fw-light">{{ $category->title }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        @include('anylearn.widget.rating', ['score' => $item->rating ?? 0])
                    </div>
                    <p><i class="text-success fa fa-calendar"></i> Khai giảng: {{ date('d/m/Y', strtotime($item->date_start)) }} {{ $num_schedule <= 1 ? '' : '(có ' . $num_schedule . ' buổi học)' }}</p>
                    <p>{{ $item->short_content }}</p>
                </div>
                <div class="row mt-3">
                    <div class="col-lg-6 col-md-6">
                        <h3>
                            @if($item->org_price > 0)
                            <span class="bg-success badge mr-1">-{{ number_format((($item->org_price - $item->price) / $item->org_price) * 100, 0,".",",") }}%</span>
                            <span class="text-secondary text-decoration-line-through mr-1">{{ number_format($item->org_price, 0, ',', '.') }}đ</span>
                            @endif
                            <span class="text-success fw-bold">{{ number_format($item->price, 0, ',', '.') }}đ</span>

                        </h3>

                    </div>
                    <div class="col-lg-6 col-md-6 d-flex">
                        <div class="flex-fill pt-2"><a @if(auth()->check()) id="add2cart-action" @endif class="border-0 btn btn-success form-control rounded-pill" href="{{ auth()->check() ? '#' : route('login') . '?cb=' . urlencode($itemServ->classUrl($item->id))   }}">Đăng ký học</a></div>
                        @if($is_fav)
                        <div class="flex-end p-2"><a class=" text-danger" href="{{ route('class.like', ['itemId' => $item->id ]) }}"><i class="fas fa-2x fa-heart"></i></a></div>
                        @else
                        <div class="flex-end p-2 text-success"><a class="text-danger" href="{{ route('class.like', ['itemId' => $item->id ]) }}"><i class="far fa-2x fa-heart"></i></a></div>
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
                <button class="nav-link text-secondary fw-bold active" id="content-tab" data-bs-toggle="tab" data-bs-target="#content" type="button" role="tab" aria-controls="content" aria-selected="true">MÔ TẢ</button>
            </li>
            @if($author->role == 'school')
            <!-- <li class="nav-item" role="presentation">
                <button class="nav-link text-secondary fw-bold" id="teachers-tab" data-bs-toggle="tab" data-bs-target="#teachers" type="button" role="tab" aria-controls="teachers" aria-selected="false">GIẢNG VIÊN</button>
            </li> -->
            @endif
            <li class="nav-item" role="presentation">
                <button class="nav-link text-secondary fw-bold" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab" aria-controls="review" aria-selected="false">ĐÁNH GIÁ</button>
            </li>
        </ul>
        <div class="tab-content border border-top-0 mb-5 shadow" id="myTabContent">
            <div class="tab-pane fade show active p-2" id="content" role="tabpanel" aria-labelledby="content-tab">
                <div class="collapse-module pb-4">
                    <div class="collapse" id="contentCollapse">
                        {!! $item->content !!}
                    </div>
                    <div class="text-center">
                        <button class="ps-4 pe-4 border-0 btn btn-white rounded-pill shadow fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#contentCollapse" aria-expanded="false" aria-controls="contentCollapse">
                    </div>
                </div>

            </div>
            @if($author->role == 'school')
            <!-- <div class="tab-pane fade p-2" id="teachers" role="tabpanel" aria-labelledby="teachers-tab">...</div> -->
            @endif
            <div class="tab-pane fade ps-4 pe-4" id="review" role="tabpanel" aria-labelledby="review-tab">
                @include('anylearn.pdp.review')
            </div>
        </div>
    </div>
    <div class="mb-3">
        @include('anylearn.home.classes', [
        'title' => 'KHOÁ HỌC LIÊN QUAN',
        'carouselId' => 'pdp-classes',
        'data' => $hotItems['list']
        ])
    </div>
</div>
@include('dialog.pdpadd2cart', ['class' => $item, 'author' => $author, 'num_schedule' => $num_schedule])
@endsection
@section('jscript')
@parent
<script>
    $('#add2cart-action').click(function() {
        $('#pdpAdd2CartModal').modal('show');
    });

    function offVoucher() {
        $("#add2cartvoucher").hide();
        $("input[name=voucher]").val("");
    }

    function onVoucher() {
        $("#add2cartvoucher").show();
    }

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
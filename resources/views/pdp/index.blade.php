@inject('itemServ','App\Services\ItemServices')
@extends('page_layout')
@section('title')
{{ $item->title }}
@endsection
@section('body')
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-4 col-md-6">
            <img style="width: 100%;" src="{{ $item->image }}" />
        </div>
        <div class="col-lg-8 col-md-6">
            @include('layout.breadcrumb', ['breadcrumb' => $breadcrumb])
            <h2 class="text-blue">{{ $item->title }}</h2>
            <div>
                @include('pdp.rating', ['score' => $item->rating])
            </div>
            <p><i class="fa fa-calendar"></i> Khai giảng: {{ date('d/m/Y', strtotime($item->date_start)) }} {{ $num_schedule <= 1 ? '' : '(có ' . $num_schedule . ' buổi học)' }}</p>
            <p><i class="fa fa-{{ $author->role == 'teacher' ? 'user' : 'university'}}"></i> {{ $author->role == 'teacher' ? 'Giảng viên' : 'Trung tâm' }}: {{ $author->name }}</p>
            <h3 class="text-orange">{{ number_format($item->price, 0, ',', '.') }}</h3>
            <div><a @if(auth()->check()) id="add2cart-action"  @endif class="btn btn-success form-control" href="{{ auth()->check() ? '#' : route('login') . '?cb=' . urlencode($itemServ->classUrl($item->id))   }}">Đăng ký học</a></div>

        </div>
    </div>
    <div class="row mt-5">
        <div class="col-sm-12">
            <div class="anylearn_content">
                {!! $item->content !!}
            </div>
        </div>
    </div>
@include('dialog.pdpadd2cart', ['class' => $item, 'author' => $author, 'num_schedule' => $num_schedule])
@endsection
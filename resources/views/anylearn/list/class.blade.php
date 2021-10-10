@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.layout')
@section('body')
@include('anylearn.widget.breadcrumb', ['breadcrumb' => $breadcrumb])
<div class="card shadow mb-5">
    <div class="card-body">

    </div>
</div>
<div class="row mb-2">
    <div class="col-md-9 grid-box">
        @if(count($classes) <= 0) <p>
            {{ $author->role == 'school' ? 'Trung tâm' : 'Chuyên gia' }} này tạm thời chưa có khoá học nào đang mở.
            </p>
            @else <ul class="row list-unstyled grid">
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

            </div>
        </div>
    </div>
</div>

@endsection
@section('jscript')
@parent

@endsection
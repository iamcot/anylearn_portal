@inject('itemServ','App\Services\ItemServices')
@extends('anylearn.layout')
@section('body')
<div class="row">
    <div class="col-md-12">
        @include('anylearn.widget.breadcrumb', ['breadcrumb' => $breadcrumb])
        @if(count($list) <= 0) @else <ul class="row list-unstyled grid">
            @foreach($list as $school)
            <li class="col-md-3 col-xs-6 mb-5">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="imagebox">
                            <img class="img-fluid" src="{{ $school->image ?? '/cdn/img/school-no-image.png' }}">
                        </div>

                        <div class="description">
                            <h5 class="grid_title">{{ $school->name }}</h5>
                            <div class="text-center">
                                <a href="{{ route('classes', ['role' => 'teacher', 'id' => $school->id ]) }}" class="btn border-0 rounded-pill btn-success">KHOÁ HỌC <i class="fa fa-chevron-right"></i></a>
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
</div>

@endsection
@section('jscript')
@parent
@endsection
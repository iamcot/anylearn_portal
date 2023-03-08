@extends('anylearn.me.layout')
@section('spmb')
    friend
@endsection
@section('body')
    <div class="p-0 card">
        <div class="bg-light card-header">
            <div class="d-flex justify-content-between">
                <h5 class="mb-0">Bạn bè ({{auth()->user()->num_friends}})</h5>
            </div>
        </div>
        <div class="bg-light px-1 pb-1 pt-0 fs--1 card-body">
            <div class="gx-0 gy-1 text-center row">
                @foreach ($friends as $row)
                <div class="col-xxl-2 col-lg-3 col-md-4 col-6">
                    <div class="bg-white dark__bg-1100 p-3 h-100"><a href="#">
                        @if ($row->image != null)
                        <img src="{{ $row->image }}" width="100"
                        class="mb-3 shadow-sm img-fluid rounded-circle img-thumbnail"></a>
                        @else
                        <img src="/cdn/img/logo.png" width="100"
                        class="mb-3 shadow-sm img-fluid rounded-circle img-thumbnail"></a>
                        @endif

                        <h6 class="mb-1"><a href="#">{{ $row->name}}</a></h6>
                        <p class="fs--2 mb-1"><a class="text-700">{{ $row->title}}</a></p>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </div>
@endsection

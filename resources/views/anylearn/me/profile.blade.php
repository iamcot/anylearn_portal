@extends('anylearn.me.layout')
<style>
    .bg-holder {
        position: absolute;
        width: 100%;
        min-height: 100%;
        top: 0;
        left: 0;
        background-size: cover;
        background-position: 50%;
        overflow: hidden;
        will-change: transform, opacity, filter;
        backface-visibility: hidden;
        background-repeat: no-repeat;
        z-index: 0;
        background-image: url("{{ $user->banner }}");
    }

    button[aria-expanded="false"]:after {
        content: 'XEM THÊM';
    }

    button[aria-expanded="true"]:after {
        content: 'THU GỌN';
    }
</style>
@section('body')
    <div class="mb-3 card">
        <div class="position-relative card-header">
            <div class="bg-holder rounded-3 rounded-bottom-0">
            </div>
            <div class="avatar avatar-5xl avatar-profile">
                <img class="rounded-circle img-thumbnail shadow-sm" src="{{ $user->image }}" alt="">
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <br>
                    <br>
                    <br>
                    <h4 class="mb-1">{{ $user->name }}<span></span></h4>
                    <h5 class="fs-0 fw-normal">{{ $user->title }}</h5>
                    <a href="/me/edit" type="button" class="px-3 btn btn-primary btn-sm">Sửa thông tin</a>
                    <a href="{{ route('me.friend') }}" type="button" class="px-3 ms-2 btn btn-secondary btn-sm">Bạn bè</a>
                    <div class="border-dashed border-bottom my-4 d-lg-none"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-3 card ">
        <div class="bg-light card-header">
            <h5 class="mb-0">Thông tin</h5>
        </div>
        <div class="text-justify text-1000 card-body">
            <div class="row">
                <div class="col-lg-4">
                    <label class="form-label" for="lastName">Họ và tên:</label> {{ $user->name }}

                </div>
                <div class="col-lg-4">
                    <label class="form-label" for="lastName">Ngày sinh:</label> {{ $user->dob }}

                </div>
                <div class="col-lg-4">
                    <label class="form-label" for="lastName">Giới tính:</label> {{ $user->sex }}

                </div>
                <div class="col-lg-4">
                    <label class="form-label" for="lastName">CMND/CCCD:</label> {{ $user->cert_id }}

                </div>
                <div class="col-lg-4">
                    <label class="form-label" for="lastName">Mã giới thiệu:</label> {{ $user->refcode }}

                </div>
                <div class="col-lg-4">
                    <label class="form-label" for="lastName">Số điện thoại:</label> {{ $user->phone }}

                </div>
                <div class="col-lg-4">
                    <label class="form-label" for="lastName">Địa Chỉ:</label> {{ $user->address }}

                </div>
            </div>
            <p>{{ $user->introduce }}</p>
            <div class="collapse" id="collapseExample">
                <p>{!! $user->full_content !!}</p>
            </div>
        </div>
        <div class="bg-light p-0 border-top d-grid card-footer">
            <button class="ps-4 pe-4 border-0 btn btn-white shadow fw-bold" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            </button>
        </div>
    </div>
@endsection

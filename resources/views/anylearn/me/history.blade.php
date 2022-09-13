@extends('anylearn.me.layout')
@section('body')
<div class="classic-tab-wrap py-3">
    <div class="heading-tab">
        <div>Số anyPoint:</div>
        <div class="row">
            <div class="col-md-8"><h3 class="text-danger">{{ $anyPoint }}</h3></div>
            <div class="col-md-4 text-end"><a href="https://anylearn.vn/helpcenter/tich-luy-diem">anyPoint là gì?</a></div>
        </div>
    </div>
    <div class="tab-wrap">
        <ul class="nav nav-tabs"  id="pills-tab" role="tablist">
            <ul class="nav nav-pills p-3 " id="pills-tab" role="tablist">
                <li class="nav-item text-center " role="presentation">
                    <a class="btn btn-default active " id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Lịch sử thanh toán</a>
                </li>
                <li class="nav-item text-center" role="presentation">
                    <a class="btn btn-default" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Lịch sử anyPoint</a>
                </li>
            </ul>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="card shadow">
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($WALLETM as $row)
                                <tr>
                                    <td class="text-start" scope="col-md-6"><b>{{ $row->content }}</b><br> {{ $row->created_at }} <br>@if($row->status==0) <b>Đang chờ</b>  @else <b class="text-danger">Đã xác nhận</b> @endif</td>
                                    <td class="text-end text-danger" scope="col-md-6"><br>{{ $row->amount}}</td>
                                </tr>
                                @endforeach 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                <div class="card shadow">
                    <div class="card-body p-0 table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                @foreach($WALLETC as $row)
                                <tr>
                                    <td class="text-start" scope="col-md-6"><b>{{ $row->content }}</b><br> {{ $row->created_at }}  <br>@if($row->status==0) <b>Đang chờ</b>  @else <b class="text-danger">Đã xác nhận</b> @endif</td>
                                    @if($row->amount >=0)
                                    <td class="text-end text-danger" scope="col-md-6"><br>+{{ $row->amount }}</td>
                                    @else
                                    <td class="text-end text-black" scope="col-md-6"><br>{{ $row->amount }}</td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>                
            </div>
        </div>
    </div>

</div>
<style>
    /**Classic Tabs Wrap**/

.classic-tab-wrap .tab-wrap {
    padding: 20px;
    background: #f5f5f5;
}

.classic-tab-wrap .tab-wrap .nav-tabs li {
    margin: 0;
}

.classic-tab-wrap .tab-wrap .nav-tabs li a {
    font-weight: 500;
    font-size: 18px;
    color: #8a8a8a;
    border: none;
    border-bottom: 2px solid transparent;
    padding-left: 35px;
    padding-right: 35px;
}

.classic-tab-wrap .tab-wrap .nav-tabs li a.active {
    color: #495057;
    background: transparent;
    border-color: #495ab7;
}

.classic-tab-wrap .tab-wrap .nav-tabs li a i {
    margin-right: 6px;
}

.classic-tab-wrap .tab-wrap .tab-pane {
    padding: 20px;
}

.viewBtn {
    padding-left: 20px;
    padding-right: 20px;
    outline: none !important;
    box-shadow: none !important;
}
</style>
@endsection
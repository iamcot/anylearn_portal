@inject('transServ','App\Services\TransactionService')
@extends('layout')

@section('body')
<form>
    <div class="card shadow mb-2">
        <div class="card-body row">
            <div class="col-xs-6 col-lg-4 ">
                <div class="form-group row">
                    <label class="col-12" for="">ID(s) <span class="small">Để trống đến ID nếu chỉ tìm 1</span></label>
                    <div class="col-lg-6 mb-1">
                        <input value="{{ app('request')->input('id_f') }}" type="text" class="form-control" name="id_f" placeholder="từ ID " />
                    </div>
                    <div class="col-lg-6">
                        <input value="{{ app('request')->input('id_t') }}" type="text" class="form-control" name="id_t" placeholder="đến ID" />
                    </div>
                </div>

            </div>
            <div class="col-xs-6 col-lg-4">
                <div class="form-group">
                    <label for="">Tên thành viên</label>
                    <input value="{{ app('request')->input('name') }}" type="text" class="form-control" name="name" placeholder="Tên thành viên" />
                </div>
            </div>

            <div class="col-xs-6 col-lg-4">
                <div class="form-group">
                    <label for="">SDT</label>
                    <input value="{{ app('request')->input('phone') }}" type="text" class="form-control" name="phone" placeholder="SDT" />
                </div>
            </div>
            <div class="col-xs-6 col-lg-4">
                <div class="form-group">
                    <label for="">Trạng thái</label>
                    <select class="form-control" name="status" id="">
                        <option value="">---TẤT CẢ---</option>
                        <option {{ app('request')->input('status') == App\Constants\OrderConstants::STATUS_PAY_PENDING ? 'selected' : '' }} value="{{ App\Constants\OrderConstants::STATUS_PAY_PENDING }}">Chờ thanh toán</option>
                        <option {{ app('request')->input('status') == App\Constants\OrderConstants::STATUS_DELIVERED ? 'selected' : '' }} value="{{ App\Constants\OrderConstants::STATUS_DELIVERED }}">Đã thanh toán</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-6 col-lg-2">
                <div class="form-group">
                    <label for="">Thanh toán</label>
                    <select class="form-control" name="payment" id="">
                        <option value="">---TẤT CẢ---</option>
                        <option {{ app('request')->input('payment') == App\Constants\OrderConstants::PAYMENT_ONEPAY ? 'selected' : '' }} value="{{ App\Constants\OrderConstants::PAYMENT_ONEPAY }}">Onepaylocal</option>
                        <option {{ app('request')->input('payment') == App\Constants\OrderConstants::PAYMENT_ATM ? 'selected' : '' }} value="{{ App\Constants\OrderConstants::PAYMENT_ATM }}">ATM</option>
                        <option {{ app('request')->input('payment') == App\Constants\OrderConstants::PAYMENT_FREE ? 'selected' : '' }} value="{{ App\Constants\OrderConstants::PAYMENT_FREE }}">Free</option>
                        <option {{ app('request')->input('payment') == 'wallet_m' ? 'selected' : '' }} value="wallet_m">Wallet_m</option>
                        <option {{ app('request')->input('payment') == 'wallet_c' ? 'selected' : '' }} value="wallet_c">Wallet_c</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-6 col-lg-3">
                <div class="form-group">
                    <label for="">Thời gian tạo từ</label>
                    <input value="{{ app('request')->input('date') }}" type="date" class="form-control" name="date" placeholder="Thời gian tạo" />
                </div>
            </div>
            <div class="col-xs-6 col-lg-3">
                <div class="form-group">
                    <label for="">Thời gian tạo đến</label>
                    <input value="{{ app('request')->input('datet') }}" type="date" class="form-control" name="datet" placeholder="Thời gian tạo đến" />
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary btn-sm" name="action" value="search"><i class="fas fa-search"></i> Tìm kiếm</button>
            <button class="btn btn-success btn-sm" name="action" value="file"><i class="fas fa-file"></i> Xuất file</button>
            <button class="btn btn-warning btn-sm" name="action" value="clear"> Xóa tìm kiếm</button>
        </div>
    </div>
</form>
<div class="card shadow">
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="">
                <tr>
                    <th class="text-center" width="5%" scope="col">#ID</th>
                    <th class="text-center">User (SDT)</th>
                    <th>Khoá học</th>
                    <th class="text-center">Số tiền</th>
                    <th class="text-center">Ưu đãi</th>
                    <th class="text-center">Giảm giá</th>
                    <th class="text-center">Thanh toán</th>
                    <th class="text-center">Trạng Thái</th>
                    <th class="text-center">Ngày</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($orders))
                @foreach($orders as $row)
                <tr>
                    <th class="text-center" scope="row">{{ $row->id }}</th>
                    <td width="15%" class="text-center" scope="row">{{ $row->name . '(' . $row->phone . ')'}}</td>
                    <td width="25%">{{ $row->classes }}</td>
                    <td class="text-center" scope="row">{{ number_format($row->amount) }}</td>
                    <td>
                        @if(!empty($row->voucher))
                        {{ $row->voucher }}
                        @elseif(!empty($row->anypoint))
                        anyPoint
                        @endif
                    </td>
                    <td>
                        @if(!empty($row->voucher))
                        {{ $row->voucher_value }}
                        @elseif(!empty($row->anypoint))
                        {{ $row->anypoint }}(~{{ $row->anypoint * 1000 }})
                        @endif
                    </td>
                    <td class="text-center" scope="row">{{ $row->payment }} <br> <span class="tooltiptext">
                    @if($row->payment =='atm')
                    Thanh toán bằng hình thức chuyển khoản
                    @elseif($row->payment =='onepaylocal')
                    Thanh toán thông qua onepay 
                    @elseif($row->payment =='free')
                    Thanh toán sử dụng voucher hoặc đổi điểm anypoint
                    @else
                    Chưa có chú thích cho phần này
                    @endif   
                    </span></td>
                    <td class="text-center" scope="row"><span class="badge badge-{{ $transServ->colorStatus($row->status) }}">{{ $row->status }}</span>
                        @if($row->status == App\Constants\OrderConstants::STATUS_PAY_PENDING)
                        <a data-orderid="{{$row->id}}" data-orderamount="{{$row->amount}}" href="{{ route('order.approve', ['orderId' => $row->id]) }}" class="btn btn-success btn-sm admin-approve col-10">Duyệt đơn</a>
                        <a href="{{ route('order.reject', ['orderId' => $row->id]) }}" class="btn btn-danger btn-sm mt-1 col-10">Từ chối</a>
                        @endif
                    </td>
                    <td class="text-center" scope="row">{{ $row->created_at }}</td>

                </tr>
                @endforeach
                @endif
            </tbody>
        </table>

    </div>
    <div class="card-footer">
        <div>{{ $orders->appends(request()->query())->links() }}</div>
    </div>
</div>
@endsection
@section('jscript')
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-170883972-1"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'UA-170883972-1', {
        'send_page_view': false
    });
    $(".admin-approve").on("click", function(event) {
        var orderId = $(this).data('orderid');
        var orderAmount = $(this).data('orderamount');
        gtag("event", "purchase", {
            "transaction_id": orderId,
            "currency": "VND",
            "value": orderAmount,
            "items": []
        });
        return;
    });
</script>
@endsection
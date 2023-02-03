@inject('itemServ','App\Services\ItemServices')
@extends('page_layout')
@section('body')

<div class="card mb-2 border-left-primary shadow">
    <div class="card-header">
        <h5 class="modal-title m-0 font-weight-bold text-primary"><i class="fa fa-shopping-cart"></i> @lang('Đơn hàng của bạn')</h5>
    </div>
    <div class="card-body p-0">
        @if(empty($order))
        <p class="p-2">@lang('Bạn không có đơn hàng nào. Nhấn nút quay lại và tìm hiểu thêm về các khoá học của anyLEARN nhé.')</p>
        @else
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th>#</th>
                    <th></th>
                    <th width="55%">@lang('Khoá học')</th>
                    <th class="text-right">@lang('Học phí')</th>
                    <th></th>
                </tr>
            <tbody>
                @foreach($detail as $item)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td><img class="img-fluid" style="max-height: 80px" src="{{ $item->image }}"></td>
                    <td>
                        @if($item->class_name)
                        {{ $item->class_name }}
                        <span class="small text-danger">({{ $item->title }} )</span>
                        @else
                        {{ $item->title }}
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->paid_price, 0, ",", ".") }}</td>
                    <td>
                        @if($api_token)
                        <a href="{{ route('checkout.remove2cart', ['odId' => $item->id, 'api_token' => $api_token ]) }}" class="btn btn-sm btn-danger" title="Xoá khỏi giỏ hàng"><i class="fa fa-trash"></i></a>
                        @else
                        <a href="{{ route('checkout.remove2cart', ['odId' => $item->id ]) }}" class="btn btn-sm btn-danger" title="Xoá khỏi giỏ hàng"><i class="fa fa-trash"></i></a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

            </thead>
        </table>
        @endif
    </div>
    <div class="card-footer">
        @if(!empty($order))
        <div class="font-weight-bold float-right">
            @if(!empty($voucherUsed))
                <span class="text-success">( Áp dụng Mã giảm giá: {{ $voucherUsed->voucher }} )</span>
            @endif
            @lang('TỔNG TIỀN:')
            <span class="text-danger">{{ number_format($order->amount, 0, ",", ".") }}</span>
        </div>
        @endif
    </div>
</div>
@if($api_token)
    <form method="POST" action="{{ route('applyvoucher', ['api_token' => $api_token]) }}" id="cartvoucher">
@else
    <form method="POST" action="{{ route('applyvoucher') }}" id="cartvoucher">
@endif
    @csrf
@if(!empty($order))
    <input type="hidden" name="order_id" value="{{ $order->id }}">
@endif
<div class="card mb-2 border-left-success shadow">
    <div class="card-body">
        <label for="" class="font-weight-bold text-success">Mã giảm giá</label>
        <div class="row">
            <div class="form-group col-6">
                <input type="text" class="form-control" name="payment_voucher" value="{{ !empty($voucherUsed) ? $voucherUsed->voucher : '' }}">
            </div>
            <div class="form-group col-4">
                @if (empty($voucherUsed))
                <button class="btn btn-success" name="cart_action" value="apply_voucher">Áp dụng</button>
                @else
                <input type="hidden" name="voucher_userd_id" value="{{ $voucherUsed->id }}">
                <button class="btn btn-danger" name="cart_action" value="remove_voucher">Huỷ</button>
                @endif
            </div>
        </div>
    </div>
</div>
</form>
@if($api_token)
    <form method="POST" action="{{ route('payment', ['api_token' => $api_token]) }}" id="cartsubmit">
@else
    <form method="POST" action="{{ route('payment') }}" id="cartsubmit">
@endif
    @csrf
@if(!empty($order))
    <input type="hidden" name="order_id" value="{{ $order->id }}">
@endif
        <div class="card mb-2 border-left-success shadow">
            <div class="card-header">
                <h5 class="modal-title m-0 font-weight-bold text-success"><i class="fa fa-wallet"></i> Phương thức Thanh toán</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    @foreach($payments as $key => $payment)
                    <li><input required type="radio" name="payment" value="{{ $key }}" id="radio_{{ $key }}"> <label for="radio_{{ $key }}"><strong>{{ $payment['title'] }}</strong></label></li>
                    @endforeach
                </ul>
                <div class="border shadow p-2 mb-2" style="max-height:150px; overflow-y: scroll;">{!! $term !!}</div>
                <p class="font-weight-bold"><input type="checkbox" name="accept_term" value="payment" id="accept_term" required> <label for="accept_term">Tôi đồng ý với điều khoản thanh toán</label></p>
            </div>
            <div class="card-footer">
                <button class="btn btn-success" name="cart_action" value="pay">THANH TOÁN</button>
            </div>
        </div>
    </form>
    @endsection
    @section('jscript')
    @parent
    <script>
        $("#cartsubmit").on("submit", function(event) {
            event.preventDefault();
            @if(!empty($order))
            gtag("event", "purchase", {
                "transaction_id": "{{ $order->id }}",
                "currency": "VND",
                "value": {{ $order -> amount }},
                "items": [
                    @foreach($detail as $item) {
                        "id": "{{ $item->item_id }}",
                        "name": "{{ $item->class_name ?? $item->title }}",
                        "price": {{ $item -> paid_price }},
                        "quantity": 1,
                        "currency": "VND"
                    }
                    @endforeach
                ]
            });
            $(this).unbind('submit').submit();
            @endif
        });
    </script>
    @endsection

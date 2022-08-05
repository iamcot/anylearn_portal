@inject('itemServ','App\Services\ItemServices')
@inject('transServ','App\Services\TransactionService')
@extends('anylearn.layout')
@if(empty($order))
@section('body')
<div class="card mb-5 shadow">
    <p class="p-5">Bạn không có đơn hàng nào. Nhấn nút quay lại và tìm hiểu thêm về các khoá học của anyLEARN nhé.</p>
</div>
@endsection
@else
@section('body')
<div class="card mb-2 border-left-primary shadow">
    <div class="card-header">
        <h5 class="modal-title m-0 text-secondary"><i class="fa fa-shopping-cart text-success"></i> Đơn hàng của bạn</h5>
    </div>
    <div class="card-body p-0">

        <table class="table table-stripped">
            <thead>
                <tr class="text-secondary">
                    <th class="text-center">#</th>
                    <th></th>
                    <th width="55%">Khoá học</th>
                    <th class="text-right">Học phí</th>
                    <th></th>
                </tr>
            <tbody>
                @foreach($detail as $item)
                <tr>
                    <td class="text-center">{{ $loop->index + 1 }}</td>
                    <td><img class="img-fluid" style="max-height: 80px" src="{{ $item->image }}"></td>
                    <td>
                        @if($item->class_name)
                        {{ $item->class_name }}
                        <span class="small text-danger">({{ $item->title }} )</span>
                        @else
                        {{ $item->title }}
                        @endif
                        @if ($item->childId != $user->id)
                        ({{ $item->childName }})
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->paid_price, 0, ",", ".") }}</td>
                    <td>
                        @if($api_token)
                        <a href="{{ route('checkout.remove2cart', ['odId' => $item->id, 'api_token' => $api_token ]) }}" class="btn btn-sm btn-danger rounded-circle" title="Xoá khỏi giỏ hàng"><i class="fa fa-trash"></i></a>
                        @else
                        <a href="{{ route('checkout.remove2cart', ['odId' => $item->id ]) }}" class="btn btn-sm btn-danger rounded-circle" title="Xoá khỏi giỏ hàng "><i class="fa fa-trash"></i></a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

            </thead>
        </table>
        @if(empty($voucherUsed))
        @if($api_token)
            <form method="POST" action="{{ route('exchangePoint', ['api_token' => $api_token]) }}" id="exchangePoint">
        @else
            <form method="POST" action="{{ route('exchangePoint') }}" id="exchangePoint">
        @endif
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                @if(!empty($pointUsed))
                <p class="ps-3 fw-bold text-secondary">Đã đổi: <span class="text-success">{{ $pointUsed->amount }} anyPoint</span>
                    <input type="hidden" name="point_used_id" value="{{ $pointUsed->id }}">
                    <button class="btn-close" name="cart_action" value="remove_point"></button>
                </p>
                @else
                <div class="p-3">
                    <label for="" class="fw-bold text-secondary">Bạn đang có <strong class="text-success">{{ $user->wallet_c }}</strong> anyPoint.
                    Bạn có thể sử dụng tối đa <strong class="text-danger">{{ $transServ->calRequiredPoint($order->amount, $user->wallet_c, $bonusRate) }}</strong> anyPoint cho đơn hàng này.
                    <a href="javascript:$('#payment_point_input').val({{ $transServ->calRequiredPoint($order->amount, $user->wallet_c, $bonusRate) }})">Dùng hết</a>
                </label>
                    <div class="row">
                        <div class="form-group col-8 col-lg-4">
                            <input type="number" min=0 max="{{ $transServ->calRequiredPoint($order->amount, $user->wallet_c, $bonusRate) }}" class=" rounded-pill form-control" id="payment_point_input" name="payment_point" value="{{ !empty($pointUsed) ? $pointUsed->amount : '' }}">
                        </div>
                        <div class="form-group col-4">
                            <button class="btn btn-success rounded-pill border-0" name="cart_action" value="exchangePoint">Sử dụng anyPoint</button>
                        </div>
                    </div>
                </div>
                @endif
            </form>
        @endif
        
        @if(empty($pointUsed))
        @if($api_token)
            <form method="POST" action="{{ route('applyvoucher', ['api_token' => $api_token]) }}" id="cartvoucher">
        @else
            <form method="POST" action="{{ route('applyvoucher') }}" id="cartvoucher">
        @endif
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                @if(!empty($voucherUsed))
                <p class="ps-3 fw-bold text-secondary">Áp dụng Mã giảm giá: <span class="text-success">{{ $voucherUsed->voucher }}</span>
                    <input type="hidden" name="voucher_userd_id" value="{{ $voucherUsed->id }}">
                    <button class="btn-close" name="cart_action" value="remove_voucher"></button>
                </p>
                @else
                <div class="p-3">
                    <label for="" class="fw-bold text-secondary">Mã giảm giá</label>
                    <div class="row">
                        <div class="form-group col-8 col-lg-4">
                            <input type="text" class=" rounded-pill form-control" name="payment_voucher" value="{{ !empty($voucherUsed) ? $voucherUsed->voucher : '' }}">
                        </div>
                        <div class="form-group col-4">
                            <button class="btn btn-success rounded-pill border-0" name="cart_action" value="apply_voucher">Áp dụng</button>
                        </div>
                    </div>
                </div>
                @endif
            </form>
        @endif
    </div>

    <div class="card-footer">
        @if(!empty($order))
        <div class="fw-bold text-secondary">
            TỔNG TIỀN:
            <span class="text-danger">{{ number_format($order->amount, 0, ",", ".") }}</span>
        </div>
        @endif
    </div>
</div>

@if($api_token)
<form method="POST" action="{{ route('payment', ['api_token' => $api_token]) }}" id="cartsubmit">
    @else
    <form method="POST" action="{{ route('payment') }}" id="cartsubmit">
        @endif
        @csrf
        @if(!empty($order))
        <input type="hidden" name="order_id" value="{{ $order->id }}">
        @endif

       
        <div class="card mt-3 mb-5 border-left-success shadow">
            <div class="card-header">
                <h5 class="modal-title m-0 text-secondary"><i class="fa fa-wallet text-success"></i> Phương thức Thanh toán</h5>
            </div>
            
            <div class="card-body">
            @if ($order->amount > 0)
                <ul class="list-unstyled">
                    <li class="p-2"><input required type="radio" name="payment" value="atm" id="radio_atm"> <label for="radio_atm"><strong>Chuyển khoản ngân hàng</strong></label></li>
                    @if(empty($saveBanks))
                    <li class="p-2"><input required type="radio" name="payment" value="onepaylocal" id="radio_onepaylocal"> <label for="radio_onepaylocal"><strong>Thanh toán trực tuyến bằng thẻ</strong></label></li>
                    @else 
                        @foreach($saveBanks as $bank)
                        <li class="p-2"><input required type="radio" name="payment" value="{{ $bank['id'] }}" id="radio_savedBank_{{ $bank['id'] }}"> <label for="radio_savedBank_{{ $bank['id'] }}"><img src = "{{ $bank['logo'] }}"> <strong>{{ substr($bank['tokenNum'], 0, 6) }}***</strong></label></li>
                        @endforeach
                        <li class="p-2"><input required type="radio" name="payment" value="onepaylocal" id="radio_onepaylocal"> <label for="radio_onepaylocal"><strong>Thanh toán bằng thẻ trực tuyến MỚI</strong></label></li>
                    @endif
                </ul>
                <p class="fw-bold" style="display: none;"  id="save_card_block"><input type="checkbox" name="save_card" id="save_card"> <label for="save_card">Lưu thông tin thẻ cho lần thanh toán sau.</label></p>
            @else 
            <input  type="hidden" name="payment" value="free">
            @endif
                <div class="border p-2 mb-2" style="max-height:150px; overflow-y: scroll;">{!! $term !!}</div>
                <p class="fw-bold"><input type="checkbox" name="accept_term" value="payment" id="accept_term" checked required> <label for="accept_term">Tôi đồng ý với điều khoản thanh toán và <a target="_BLANK" href="/privacy">chính sách bảo mật</a> của Công ty</label></p>
                <button class="btn btn-success border-0 rounded-pill mt-2" name="cart_action" value="pay">THANH TOÁN</button>

            </div>
        </div>
      
    </form>
@endsection
@endif
@section('jscript')
    @parent
    <script>
        
        $("input[name=payment]").on("click", function(event) {
            var value = $(this).val();
            if (value == "onepaylocal") {
                $("#save_card").prop("checked", true);
                $("#save_card_block").show();
            } else {
                $("#save_card_block").hide();
                $("#save_card").prop("checked", false);
            }
        });
    </script>
@endsection

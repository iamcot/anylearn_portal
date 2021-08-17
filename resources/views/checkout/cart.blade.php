@inject('itemServ','App\Services\ItemServices')
@extends('page_layout')
@section('body')
<form method="POST" action="{{ route('payment') }}">
    @csrf
<div class="card mb-5 border-left-primary shadow">
    <div class="card-header"><h5 class="modal-title m-0 font-weight-bold text-primary"><i class="fa fa-shopping-cart"></i> Đơn hàng của bạn</h5>
</div>
    <div class="card-body p-0">
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th>#</th>
                    <th></th>
                    <th width="55%">Khoá học</th>
                    <th class="text-right">Học phí</th>
                    <th></th>
                </tr>
                <tbody>
                @foreach($detail as $item)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td><img class="img-fluid" style="max-height: 80px" src="{{ $item->image }}"></td>
                    <td>{{ $item->title }} </td>
                    <td class="text-right">{{ number_format($item->paid_price, 0, ",", ".") }}</td>
                    <td>
                        <a href="{{ route('checkout.remove2cart', ['odId' => $item->id ]) }}" class="btn btn-sm btn-danger" title="Xoá khỏi giỏ hàng"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
                @endforeach
                </tbody>
              
            </thead>
        </table>
    </div>
    <div class="card-footer">
        <div class="font-weight-bold float-right">
            TỔNG TIỀN:
            <span class="text-danger">{{ number_format($order->amount, 0, ",", ".") }}</span>
        </div>
    </div>
</div>
<div class="card mb-5 border-left-danger shadow">
    <div class="card-header"><h5 class="modal-title m-0 font-weight-bold text-danger"><i class="fa fa-wallet"></i> Thanh toán</h5></div>
    <div class="card-body">
        <ul class="list-unstyled">
            @foreach(config('payment') as $key => $payment) 
            <li><input required type="radio" name="payment" value="{{ $key }}" id="radio_{{ $key }}"> <label for="radio_{{ $key }}"><strong>{{ $payment['title'] }}</strong></label></li>
            @endforeach
        </ul>
        <div class="border shadow p-2 mb-2" style="max-height:150px; overflow-y: scroll;">{!! $term !!}</div>
        <p class="font-weight-bold"><input type="checkbox" name="accept_term" value="payment" id="accept_term" required> <label for="accept_term">Tôi đồng ý với điều khoản thanh toán</label></p>
    </div>
    <div class="card-footer">
        <button class="btn btn-danger">THANH TOÁN</button>
    </div>
</div>

@endsection
@section('jscript')
@parent

@endsection
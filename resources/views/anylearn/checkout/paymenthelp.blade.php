@extends('anylearn.layout')
@inject('qrServ', 'App\Services\QRServices')
@section('spmb')
checkout-paymenthelp
@endsection
@section('body')
<h5 class="mb-5 font-weight-bold text-success">@lang('Để hoàn tất thanh toán, quý khách có thể lựa chọn')</h5>
@foreach ($banks as $bank)
<div class="card shadow mb-5 border-left-success">
    <div class="card-body">
        <div class="row">
            <div class="col-xs-12 col-md-4 mb-3">
                <h6 class="mb-2 font-weight-bold text-success">@lang('Quét mã QR')</h6>
                {!! QrCode::size(200)->generate($qrServ->QR($orderAmount, $orderId)) !!}
            </div>
            <div class="col-xs-12 col-md-8">
                <h6 class="mb-2 font-weight-bold text-success">@lang('Hoặc chuyển khoản theo thông tin')</h6>
                <dl class="row">
                    <dt class="col-sm-3">@lang('Ngân hàng')</dt>
                    <dd class="col-sm-9">{{ $bank['bank_name'] }}</dd>

                    <dt class="col-sm-3">@lang('Chi nhánh')</dt>
                    <dd class="col-sm-9">
                        {{ $bank['bank_branch'] }}
                    </dd>
                    <dt class="col-sm-3">@lang('Số tài khoản')</dt>
                    <dd class="col-sm-9">{{ $bank['bank_no'] }}</dd>
                    <dt class="col-sm-3">@lang('Người thụ hưởng')</dt>
                    <dd class="col-sm-9">{{ $bank['account_name'] }}</dd>
                    <dt class="col-sm-3">@lang('Nội dung chuyển')</dt>
                    <dd class="col-sm-9">{{ $bank['content'] }} #{{ $orderId }}</dd>
                    <dt class="col-sm-3">@lang('Số tiền')</dt>
                    <dd class="col-sm-9">{{ number_format($orderAmount, 0, ',', '.') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endforeach

<p class="small mt-2">* @lang('Các khoá học đăng ký của quý khách sẽ được cập nhật sau khi Công ty xác nhận chuyển khoản.')</p>
@endsection
@extends('anylearn.layout')
@section('body')
    <h5 class="mb-5 font-weight-bold text-success">@lang('Để hoàn tất thanh toán, quý khách vui lòng chuyển khoản theo thông tin sau.')</h5>
    @foreach ($banks as $bank)
        <div class="card shadow mb-5 border-left-success">
            <div class="card-body">
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
                    <dt class="col-sm-3">@lang('Nội dung tin chuyển tiền')</dt>
                    <dd class="col-sm-9">{{ $bank['content'] }} #{{ $orderId }}</dd>
                    <dt class="col-sm-3">@lang('Số tiền')</dt>
                    <dd class="col-sm-9">{{ number_format($orderAmount, 0, ',', '.') }}</dd>
                </dl>
            </div>
        </div>
    @endforeach

    <p class="small">* @lang('Các khoá học của quý khách sẽ tự động xác nhận sau khi Công ty xác nhận chuyển khoản.')</p>
@endsection

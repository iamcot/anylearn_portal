@inject('userServ','App\Services\UserServices')
@extends('anylearn.me.layout')
@section('spmb')
pending_orders
@endsection
@section('body')
{{-- <div class="card shadow">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover">
            <thead class="table-secondary text-secondary">
                <tr>
                    <th class="text-center border-0" width="5%" scope="col">#ID</th>
                    <th class="border-0">@lang('Khoá học')</th>
                    <th class="text-center border-0">@lang('Số tiền')</th>
                    <th class="text-center border-0">@lang('Ngày')</th>
                    <th width="15%" class="text-right border-0" scope="col">@lang('Thao tác')</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($orders))
                @foreach($orders as $row)
                <tr>
                    <th class="text-center" scope="row">{{ $row->id }}</th>
                    <td>{{ $row->classes }}</td>
                    <td class="text-center" scope="row">{{ number_format($row->amount) }}</td>
                    <td class="text-center" scope="row">{{ $row->created_at }}</td>
                    <td class="text-right">
                        <a href="{{ route('checkout.paymenthelp', ['order_id' => $row->id]) }}" class="btn btn-success btn-sm border-0 rounded-pill">Thanh toán</a>
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <div>{{ $orders->links() }}</div>
    </div>
</div> --}}
  <div class="container">
    <h1 class="text-center mb-4">Khoá học đang chờ thanh toán</h1>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tên khóa học</th>
          <th>Số tiền</th>
          <th>Ngày</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @if(!empty($orders))
                @foreach($orders as $row)
        <tr>
          <td>{{ $row->id }}</td>
          <td width="50%">{{ $row->classes }}</td>
          <td>{{ number_format($row->amount) }} đồng</td>
          <td>{{ $row->created_at }}</td>
          <td><a href="{{ route('checkout.paymenthelp', ['order_id' => $row->id]) }}" class="btn btn-success btn-sm border-0 rounded-pill">Thanh toán</a></td>
        </tr>
        @endforeach
                @endif
      </tbody>
    </table>
    <div>{{ $orders->links() }}</div>
  </div>
  {{-- <div class="container">
    <h1 class="text-center mb-4">Rút tiền</h1>
    <form>
      <div class="form-group">
        <label for="inputContractNo">Số hợp đồng:</label>
        <input type="text" class="form-control" id="inputContractNo" placeholder="Nhập số hợp đồng">
      </div>
      <div class="form-group">
        <label for="inputCurrentBalance">Số tiền hiện có:</label>
        <input type="text" class="form-control" id="inputCurrentBalance" placeholder="Nhập số tiền hiện có" readonly>
      </div>
      <div class="form-group">
        <label for="inputReceiverAccount">Tài khoản nhận tiền:</label>
        <input type="text" class="form-control" id="inputReceiverAccount" placeholder="Nhập tài khoản nhận tiền">
      </div>
      <div class="form-group">
        <label for="inputAmount">Số tiền muốn rút:</label>
        <input type="number" class="form-control" id="inputAmount" placeholder="Nhập số tiền muốn rút">
      </div>
      <div class="form-group">
        <label for="inputPassword">Mật khẩu:</label>
        <input type="password" class="form-control" id="inputPassword" placeholder="Nhập mật khẩu">
      </div>
      <button type="submit" class="btn btn-primary">Xác nhận rút tiền</button>
    </form>
  </div> --}}


@endsection

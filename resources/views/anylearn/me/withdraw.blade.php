@extends('anylearn.me.layout')
@section('spmb')
    withdraw
@endsection
@section('body')
    <div class="row crm mb-5">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <div class="imagebox border">
                                @if ($user->image)
                                    <img src="{{ $user->image }}" class="rounded" alt="{{ $user->name }}">
                                @else
                                    <i class="fa fa-user fa-2x"></i>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-9">
                            <h3><span class="text-dark">{{ $user->name }}</span>
                            </h3>
                            <div>Thành viên từ:
                                {{ $user->is_registered == 0 ? 'Chưa đăng ký' : date('d/m/Y', strtotime($user->created_at)) }}
                            </div>
                            <div>Số Tiền Hiện Có: <strong
                                    class="text-danger">{{ number_format($user->wallet_m, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow mt-3">
                <div class="card-header"><strong>Lịch Sử Giao Dịch</strong></div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th width=40%>Nội Dung</th>
                                <th>Số Tiền</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $row)
                            <tr>
                                <td>{{ $row->created_at}}</td>
                                <td>{{ $row->content }}</td>
                                <td>{{ number_format($row->amount) }}</td>
                                @if ($row->status == 1)
                                <td><span class="badge badge-success">Đã duyệt</span></td>
                                @else
                                <td><span class="badge badge-warning">Đợt duyệt</span></td>
                                @endif

                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-body">
                    <div class="container">
                        <h1 class="my-4" id="titleform">Rút tiền</h1>
                        <form>
                            @csrf
                            <div class="form-group">
                                <label for="inputCurrentBalance">Số tiền hiện có:</label>
                                <input type="text" class="form-control"
                                    value="{{ number_format($user->wallet_m  - $totalAmount)}}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="inputCurrentBalance">Ngân Hàng:</label>
                                <input type="text" class="form-control"
                                    value="{{ $contract->bank_name}}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="inputCurrentBalance">Chi Nhánh:</label>
                                <input type="text" class="form-control"
                                    value="{{ $contract->bank_branch}}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="inputCurrentBalance">Người Hưởng Thụ:</label>
                                <input type="text" class="form-control"
                                    value="{{ $contract->bank_account}}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="inputReceiverAccount">Tài khoản nhận tiền:</label>
                                <input type="text" class="form-control" id="inputReceiverAccount"
                                    placeholder="{{ $contract->bank_no }}" readonly>
                            </div>
                            <div class="form-group">
                                <label for="inputAmount">Số tiền muốn rút:</label>
                                <input type="number" class="form-control" name="withdraw" id="inputAmount"
                                    placeholder="Nhập số tiền muốn rút" required>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword">Mật khẩu:</label>
                                <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Nhập mật khẩu">
                            </div>
                            <button type="submit" class="btn btn-primary float-right" name="action" value="withdraw">Xác nhận rút tiền</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('jscript')
@parent
<script>
    $('#inputAmount').attr('max', {{ $user->wallet_m - $totalAmount}});
</script>
@endsection

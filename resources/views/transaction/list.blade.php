@inject('transServ','App\Services\TransactionService')
@extends('layout')

@section('rightFixedTop')
<form class="row">
    <div class="col-xs-4 mr-1">
        <select class="form-control" name="t" id="">
            <option {{ app('request')->input('t') == 'type' ? 'selected' : '' }} value="type">Loại giao dịch (deposit, withdraw)</option>
            <option {{ app('request')->input('t') == 'status' ? 'selected' : '' }} value="status">Trạng thái (0, 1, 99)</option>
        </select>
    </div>
    <div class="col-xs-7 mr-1">
        <input value="{{ app('request')->input('s') }}" type="text" class="form-control" name="s" placeholder="{{ __('Tìm kiếm') }}" />
    </div>
    <div class="col-xs-1">
        <button class="btn btn-primary btn"><i class="fas fa-search"></i></button>
    </div>
</form>
@endsection

@section('body')
<div class="card shadow">
    <div class="card-body p-0 table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="">
                <tr>
                    <th class="text-center" width="5%" scope="col">#ID</th>
                    <th class="text-center">User (SDT)</th>
                    <th class="text-center">Loại</th>
                    <th class="text-center">Số tiền</th>
                    <th class="text-center">Ngân hàng</th>
                    <th class="text-center">Cập nhật</th>
                    <th width="15%" class="text-right" scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($transaction))
                @foreach($transaction as $row)
                <tr>
                    <th class="text-center" scope="row">{{ $row->id }}</th>
                    <td class="text-center" scope="row">{{ $row->user->name }} ({{ $row->user->phone }})</td>
                    <td class="text-center" scope="row">{{ $row->type }}</td>
                    <td class="text-center" scope="row">{{ number_format($row->amount) }}</td>
                    <td class="text-center" scope="row">{{ $row->pay_info }}</td>
                    <td class="text-center">{{ date('H:i d/m/y', strtotime($row->updated_at)) }}</td>
                    <td class="text-right">
                        {!! $transServ->statusOperation($row->id, $row->status) !!}
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
        <!-- <div class="small ml-3">
            <p><i class="fas fa-fire text-danger" title="Nổi bật"></i> Thành viên nổi bật. <i class="fas fa-check-circle text-success"></i> Thành viên đang hoạt động. <i class="fas fa-stop-circle text-danger"></i> Thành viên đang bị khóa.
                <i class="fas fa-cloud-upload-alt text-gray"></i> Giấy tờ chưa hợp lệ. <i class="fas fa-cloud-upload-alt text-success"></i> Đã cập nhật chứng chỉ, giấy tờ >>> Click để xem chi tiết.

            </p>
        </div> -->

    </div>
    <div class="card-footer">
        <div>{{ $transaction->links() }}</div>
    </div>
</div>
@endsection
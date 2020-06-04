@inject('transServ','App\Services\TransactionService')
@extends('layout')

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
                    <th class="text-center">Nội dung</th>
                    <th class="text-center">Cập nhật</th>
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
                    <td class="text-center" scope="row">{{ $row->content }}</td>
                    <td class="text-center">{{ date('H:i d/m/y', strtotime($row->updated_at)) }}</td>
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
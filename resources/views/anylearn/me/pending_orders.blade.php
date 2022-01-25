@inject('userServ','App\Services\UserServices')
@extends('anylearn.me.layout')

@section('body')
<div class="card shadow">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover">
            <thead class="table-secondary text-secondary">
                <tr>
                    <th class="text-center border-0" width="5%" scope="col">#ID</th>
                    <th class="border-0">Khoá học</th>
                    <th class="text-center border-0">Số tiền</th>
                    <th class="text-center border-0">Ngày</th>
                    <th width="15%" class="text-right border-0" scope="col">Thao tác</th>
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
</div>
@endsection
@inject('transServ','App\Services\TransactionService')
@extends('layout')

@section('body')
<div class="row">
    <div class="col-xs-12 col-sm-3"></div>
    <div class="col-xs-12 col-sm-9">
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
                            <td class="text-center" scope="row">@if(!empty($row->user)) {{ $row->user->name }} ({{ $row->user->phone }}) @endif</td>
                            <td class="text-center" scope="row">{{ $row->type }}</td>
                            <td class="text-center" scope="row">{{ number_format($row->amount) }}</td>
                            <td class="text-center" scope="row">{{ $row->content }}</td>
                            <td class="text-center">{{ date('H:i d/m/y', strtotime($row->updated_at)) }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>

            </div>
            <div class="card-footer">
                <div>{{ $transaction->links() }}</div>
            </div>
        </div>
    </div>
</div>

@endsection
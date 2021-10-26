@inject('userServ','App\Services\UserServices')
@extends('anylearn.me.layout')

@section('body')
@if(empty($notifications))
<p>Bạn chưa có đơn hàng nào</p>
@else
<div class="card shadow">
<table class="table table-borderless table-striped">
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $userServ->timeAgo($order->created_at) }}</td>
                <td>
                   
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="p-3">
{{ $orders->links() }}
</div>
</div>
@endif
@endsection
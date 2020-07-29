@extends('layout')
@section('rightFixedTop')
<form class="row">
    <div class="col-xs-2 mr-1">
        <a class="btn btn-success" href="{{ route('config.voucher.create') }}"><i class="fas fa-plus"></i> @lang('Thêm mới')</a>
    </div>
</form>
@endsection


@section('body')
@if(sizeof($vouchers) == 0)
<div class="text-center mt-5 pt-5">
    @lang('Chưa có voucher nào.')
</div>
@else
<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-bordered table-striped">
            <thead>
                <thead>
                    <th class="text-center">#ID</th>
                    <th>Mã voucher</th>
                    <th>Giá trị</th>
                    <th>Số lượng</th>
                    <th>Thời hạn</th>
                    <th>Lần sửa cuối</th>
                    <th>Thao tác</th>
                </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                <tr>
                    <th class="text-center">{{ $voucher->id }}</th>
                    <td>{{ $voucher->voucher }}</td>
                    <td>{{ $voucher->value }}</td>
                    <td>{{ $voucher->amount }}</td>
                    <td></td>
                    <td>{{ $voucher->updated_at }}</td>
                    <td>
                        <a href="{{ route('config.voucher.edit', ['id' => $voucher->id]) }}">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
    </div>
    <div class="card-footer">
    </div>
</div>

@endif

@endsection
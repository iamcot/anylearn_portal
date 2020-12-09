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
    <div class="card-body p-0 table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <thead>
                    <th class="text-center">#ID</th>
                    <th>Loại voucher</th>
                    <th>Cách tạo</th>
                    <th>Mã/Tiền tố voucher</th>
                    <th>Giá trị</th>
                    <th>Số lượng</th>
                    <th>Tạo lúc</th>
                    <th>Thao tác</th>
                </thead>
            <tbody>
                @foreach($vouchers as $voucher)
                <tr>
                    <th class="text-center">{{ $voucher->id }}</th>
                    <td>{{ $voucher->type }}</td>
                    <td>{{ $voucher->generate_type }}</td>
                    <td>{{ $voucher->prefix }}</td>
                    <td>{{ $voucher->value }}</td>
                    <td>{{ $voucher->qtt }}</td>
                    <td>{{ $voucher->updated_at }}</td>
                    <td>
                        <a class="btn btn-sm btn-info" href="{{ route('config.voucher.list', ['id' => $voucher->id]) }}">
                            Danh sách
                        </a>
                        <a class="btn btn-sm btn-{{ $voucher->status == 1 ? 'danger' : 'success' }}" href="{{ route('config.voucher.close', ['id' => $voucher->id, 'type' => 'group']) }}">
                        {{ $voucher->status == 1 ? 'Khóa' : 'Mở' }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </thead>
        </table>
    </div>
    <div class="card-footer">
        {{ $vouchers->links() }}
    </div>
</div>

@endif

@endsection
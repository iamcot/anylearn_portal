@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')
@extends('layout')
@section('rightFixedTop')
    <div class="col-xs-2 mr-1">
        <a class="btn btn-success btn-sm border-0 rounded-pill" href="#"><i class="fas fa-plus">
            </i> <span class="mobile-no-text"> @lang('Thêm mới')</span></a>
    </div>
@endsection
@section('body')
    <div class="card shadow">
        <div class="card-body p-0 table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <th class="text-center">#</th>
                    <th>Code</th>
                    <th>ItemID</th>
                    <th>UserID</th>
                    <th>Đơn hàng</th>    
                    <th>Cập nhật</th>
                    <th width="16%">Thao tác</th>
                </thead>
                <tbody>
                    @foreach ($itemCodes as $code)
                        <tr>
                            <th class="text-center">{{ $code->id }}</th>
                            <td>{{ $code->code }}</td>
                            <td>{{ $code->user_id }}</td>
                            <td>{{ $code->item_id }}</td>
                            <td>{{ $code->order_detail_id }}</td>
                            <td>{{ $code->updated_at }}</td>
                            <td>
                                <a class="btn btn-sm btn-info mt-1" href="{{ route('class', []) }}"><i class="fa fa-paper-plane"></i> Gửi lại</a>
                                <a class="btn btn-sm btn-info mt-1" href="{{ route('class', []) }}"><i class="	fa fa-bolt"></i> Làm mới</a> 
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $itemCodes->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
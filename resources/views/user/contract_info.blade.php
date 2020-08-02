@inject('userServ','App\Services\UserServices')
@extends('layout')

@section('body')
<p>
    Sử dụng chính xác tên các trường thông tin kèm cặp dấu {} để đặt vào các vị trí cần đổ thông tin khi
    <a target="_blank" href="/admin/config/guide/contract_teacher">Sửa mẫu hợp đồng Giảng viên</a>, hoặc
    <a target="_blank" href="/admin/config/guide/contract_school">Sửa mẫu hợp đồng Trường học</a>. Ví dụ: {name}, {cert_id} ...
</p>
<form method="post" class="row">
    @csrf
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                Thông tin hợp đồng
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <tr>
                        <th width="40%">Trường thông tin</th>
                        <th>Dữ liệu</th>
                    </tr>
                    @foreach(json_decode(json_encode($contract), true) as $key => $value)
                    <tr>
                        <th>{{ $key }}</th>
                        <td>{{ $value }}</td>
                    </tr>
                    @endforeach
                </table>

            </div>
            <div class="card-footer">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">
                Chữ ký
                @if($contract->status == \App\Constants\UserConstants::CONTRACT_SIGNED)
                <button class="btn btn-sm btn-primary float-right" name="action" value="{{ \App\Constants\UserConstants::CONTRACT_APPROVED }}">Duyệt hợp đồng</button>
                @endif
            </div>
            <div class="card-body">
                @if($contract->signed)
                <img class="img-fluid" src="{{ $contract->signed }}" alt="" style="max-height:400px;">
                @else
                <p>Chưa có chữ ký</p>
                @endif
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-danger float-right" name="action" value="{{ \App\Constants\UserConstants::CONTRACT_DELETED }}">Từ chối hợp đồng</button>
            </div>
        </div>
    </div>
</form>
@endsection
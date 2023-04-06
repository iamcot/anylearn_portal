@extends('anylearn.me.layout')
@section('spmb')
    work
@endsection
@section('body')
    <div class="container">
        <h1 class="text-center">Hoạt động người dùng</h1>
        <form>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Người đăng ký</th>
                        <th>Loại hành động</th>
                        <th>Ngày thực hiện</th>
                        <th>Note</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        @if ($row->type == 'trial')
                        <td>Học thử</td>
                        @elseif ($row->type == 'visit')
                        <td>Tham quan </td>
                        @elseif ($row->type == 'test')
                        <td>Kiểm tra</td>
                        @endif
                        <td>{{ date_format(date_create($row->date), 'Y-m-d') }}</td>
                        <td>{{ $row->note }}</td>
                        @if ($row->status == 1)
                        <td><span class="badge bg-success">Thành công</span></td>
                        @else
                        <td><span class="badge bg-warning text-dark">Đang chờ</span></td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    </div>
@endsection

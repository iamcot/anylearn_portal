@extends('layout')
@section('body')
<div class="card shadow mb-3">
    <div class="card-body row">
        <div class="col-xs-6 col-lg-2">
            <div class="form-group">
                <label for="">ID</label>
                <input value="" type="text" class="form-control" name="user_id" placeholder="ID người dùng">
            </div> 
        </div>
        <div class="col-xs-6 col-lg-2">
            <div class="form-group">
                <label for="">Loại</label>
                <input value="" type="text" class="form-control" name="user_type" placeholder="Loại người dùng">
            </div>
        </div>
        <div class="col-xs-6 col-lg-2">
            <div class="form-group">
                <label for="">Từ ngày</label>
                <input value="" type="date" class="form-control" name="date_from">
            </div>
        </div>
        <div class="col-xs-6 col-lg-2">
            <div class="form-group"> 
                <label for="">Đến ngày</label>
                <input value="" type="date" class="form-control" name="date_to">
            </div>
        </div>
        <div class="col-xs-6 col-lg-2 d-flex flex-column justify-content-end mb-3">   
            <button class="btn btn-primary align-items-center" name="action" value="search">Filter</button>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-bordered table-responsive table-striped">
            <thead>
                <tr>
                    @foreach($columns as $col)
                    <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($spms as $row)
                <tr>
                    @foreach(json_decode(json_encode($row), true) as $spmcol)
                    <td>
                        {{ substr($spmcol, 0, 50) }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    <div class="card-footer">{{ $spms->links() }}</div>
</div>

@endsection
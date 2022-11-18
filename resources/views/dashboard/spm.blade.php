@extends('layout')
@section('body')
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
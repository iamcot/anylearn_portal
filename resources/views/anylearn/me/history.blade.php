@extends('anylearn.me.layout')
@section('body')
    <div class="card p-3">
        <div class="strong">anyPoint:</div>
        <div class="row">
            <div class="col-md-8">
                <h3 class="text-danger fs-3">{{ $anyPoint }}</h3>
            </div>
            <div class="col-md-4 text-end"><a target="_blank" href="https://anylearn.vn/helpcenter/tich-luy-diem">anyPoint là
                    gì?</a></div>
        </div>
    </div>
    <ul class="nav nav-tabs" id="classtab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary active" id="done-tab"
                data-bs-toggle="tab" data-bs-target="#done" type="button" role="tab" aria-controls="done"
                aria-selected="true">Lịch sử thanh toán</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link text-secondary" id="open-tab"
                data-bs-toggle="tab" data-bs-target="#open" type="button" role="tab" aria-controls="open"
                aria-selected="true">Lịch sử anyPoint</button>
        </li>
    </ul>
    <div class="tab-content border border-top-0 mb-5 shadow bg-white" id="myTabContent">
        <div class="tab-pane fade {{ session('tab', 'open') == 'done' ? 'show active' : '' }} p-2" id="done"
            role="tabpanel" aria-labelledby="done-tab">
            <table class="table text-secondary table-hover">
                <tbody>
                    @foreach ($WALLETM as $row)
                    <tr>
                        <td class="text-start" scope="col-md-6"><b>{{ $row->content }}</b><br>
                            {{ $row->created_at }} <br>
                            @if ($row->status == 0)
                                <b>Đang chờ</b>
                            @else
                                <b class="text-danger">Đã xác nhận</b>
                            @endif
                        </td>
                        <td class="text-end text-danger" scope="col-md-6"><br>{{ abs($row->amount) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade {{ session('tab', 'open') == 'open' ? 'show active' : '' }} p-2" id="open"
            role="tabpanel" aria-labelledby="open-tab">

            <table class="table  text-secondary table-hover">
                
                <tbody>
                    @foreach ($WALLETC as $row)
                    <tr>
                        <td class="text-start" scope="col-md-6"><b>{{ $row->content }}</b><br>
                            {{ $row->created_at }} <br>
                            @if ($row->status == 0)
                                <b>Đang chờ</b>
                            @else
                                <b class="text-danger">Đã xác nhận</b>
                            @endif
                        </td>
                        @if ($row->amount >= 0)
                            <td class="text-end text-danger" scope="col-md-6"><br>+{{ abs($row->amount) }}
                            </td>
                        @else
                            <td class="text-end text-black" scope="col-md-6"><br>{{ abs($row->amount) }}
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

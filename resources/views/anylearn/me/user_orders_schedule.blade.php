@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')

@extends('anylearn.me.layout')
@section('spmb')
    orders
@endsection
@section('body')
    <div class="container mt-4">
        <h1 class="mb-4">Lịch học của tôi</h1>
        
        <div>
            <h2>Danh sách các buổi học</h2>
            <table class="table" id="myTable">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Ngày học</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daylist as $key => $date)
                    @if ($date < $currentDate)
                    <tr style="background: #ddd">
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $date }}</td>
                    </tr>
                    @else 
                    <tr style="background: #4fd172">
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $date }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        </div>
@endsection


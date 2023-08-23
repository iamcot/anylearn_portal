@inject('userServ', 'App\Services\UserServices')
@inject('itemServ', 'App\Services\ItemServices')

@extends('anylearn.me.layout')
@section('spmb')
orders
@endsection
@section('body')
<div class="container mt-4 p-4 bg-white shadow-sm p-3 mb-5 bg-white rounded">
    @if($item->subtype == 'digital')
    @if (!empty($code))
    <p>Mã kích hoạt của bạn là: <strong>{{ $code }}</strong></p>
    @else
    <p>Tạm thời chưa có mã kịch hoạt, bạn vui lòng liên hệ với hotline anyLEARN để được hỗ trợ</p>
    @endif
    @elseif($item->subtype == 'video')
    <p>Xem khóa học video tại <a href="{{ $itemServ->classUrl($item->id) }}" target="_blank">Trang khóa học</a></p>
    @elseif(!empty($schedule))
    <h1 class="mb-4">Lịch học của tôi</h1>

    <div>
        <h2>Danh sách các buổi học</h2>
        <table class="table table-striped text-secondary" id="myTable">
            <thead>
                <tr>
                    <th class="text-center">STT</th>
                    <th>Ngày học</th>
                    <th>Thời gian</th>
                    <th>Địa điểm</th>
                    <th class="text-center">Bản đồ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daylist as $key => $date)
                <tr {{--style="{{ $date < $currentDate ? 'background: #ddd' : 'background: #4fd172'}}"--}}>
                    <td class="text-center">{{ $key + 1 }}</td>
                    <td>{{ $date }}</td>
                    <td>{{ $schedule->time_start }} - {{ $schedule->time_end }}</td>
                    @if (!empty($location))
                    <td>{{ $location->address }} - {{ $location->ward_path }}</td>
                    <td class="text-center"><a href="https://www.google.com/maps/search/?api=1&query={{ $location->latitude }},{{ $location->longitude }}" target="_blank" rel="noopener noreferrer">@lang('Xem')</a> </td>
                    @else
                    <td>Chưa cập nhật</td>
                    <td></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>Khóa học chưa có lịch học nào. Vui lòng kiểm tra với anyLEARN qua hotline hoặc trực tiếp với đối tác.</p>
    @endif
</div>
@endsection
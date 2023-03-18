@if (!empty($orderData))
<div style="margin-left: 20px;color: #000; font-size: 16px; line-height: 24px;">
    <p>Thông tin khóa học: {{ $orderData->title}}</p>
    <p>Học Phí: {{number_format($orderData->unit_price)}}đ</p>
    <p>Học Viên: {{$orderData->childName}}</p>
    <p>
        @if($orderData->plan_location_name)
        Học tại {{ $orderData->plan_location_name }}; @foreach(explode(",", $orderData->plan_weekdays) as $day ) {{ $day == 1 ? __('Chủ Nhật') : __("Thứ " . ($day)) }} {{ !$loop->last ? ", " : ". " }} @endforeach
        Bắt đầu từ {{ date("d/m/Y", strtotime($orderData->plan_date_start)) }}
        @else
        Bắt đầu từ ngày {{ date('d/m/Y', strtotime($orderData->date_start)) }}
        @endif
    </p>
    @if(!empty($extraFee) && count($extraFee) > 0)
    <p>Phụ phí:
    <ul>
        @foreach($extraFee as $extra)
        <li> {{ $extra->title}}: {{ number_format($extra->price) }}₫</li>
        @endforeach
    </ul>
    </p>
    @endif
</div>
@endif
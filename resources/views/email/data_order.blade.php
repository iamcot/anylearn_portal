
    @if (!empty($data))
    @foreach ($data as $row)
    <div style="margin-left: 20px; margin-right: 20px">
        <div style="mso-line-height-rule: exactly; mso-text-raise: 11px; vertical-align: middle">
            <p class="v1size-16"
                style="margin-top: 0; margin-bottom: 20px; font-size: 16px; line-height: 24px; text-align: center"
                lang="x-size-16"><span style="color: #0d0d0d">Thông tin khóa học: {{ $row->item_title}}</span></p>
                <br>
            <p>Học Phí: {{number_format($row->item_price)}}đ</p>
            <p>Học Viên: {{$row->username}}</p>
            <p>Phụ Phí: {{$row->item_extra_title }} - {{number_format($row->item_extra_price) or 0}}đ</p>
            <p>Ngày Khai Giảng: {{$row->schedule_date}} - {{ $row->schedule_time }} - {{ $row->schedule_content}}</p>
            <p>{{$row->mailcontent}}</p>
        </div>
    </div>
    <div style="margin-left: 20px; margin-right: 20px">
        <div style="mso-line-height-rule: exactly; line-height: 1px; font-size: 1px">
            &nbsp;</div>
    </div>
    @endforeach
    @endif

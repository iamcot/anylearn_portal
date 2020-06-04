<?php

use App\Constants\ConfigConstants;

return [
    ConfigConstants::CONFIG_FRIEND_TREE => [
        'title' => 'Số cấp của cây thành viên',
        'hint' => 'Chỉ đếm gián tiếp',
        'type' => 'number',
        'value' => 3
    ],
    ConfigConstants::CONFIG_NUM_COURSE => [
        'title' => 'Số khóa học nổi bật',
        'hint' => 'S/lg hiển thị trên trang chủ',
        'type' => 'number',
        'value' => 6
    ],
    ConfigConstants::CONFIG_NUM_TEACHER => [
        'title' => 'Số chuyên gia nổi bật',
        'hint' => 'S/lg hiển thị trên trang chủ',
        'type' => 'number',
        'value' => 6
    ],
    ConfigConstants::CONFIG_NUM_SCHOOL => [
        'title' => 'Số trung tâm nổi bật',
        'hint' => 'S/lg hiển thị trên trang chủ',
        'type' => 'number',
        'value' => 6
    ],
    ConfigConstants::CONFIG_FEE_MEMBER => [
        'title' => 'Phí hàng tháng của người dùng',
        'hint' => '',
        'type' => 'number',
        'value' => 930000
    ],
    ConfigConstants::CONFIG_COMMISSION => [
        'title' => 'Tỉ lệ commission gián tiếp',
        'hint' => 'Nhập số thập phân. vd: 0.05 (tương đương 5%)',
        'type' => 'text',
        'value' => 0.05
    ],
    ConfigConstants::CONFIG_DISCOUNT => [
        'title' => 'Tỉ lệ commission trực tiếp mua hàng',
        'hint' => 'Nhập số thập phân. vd: 0.1 (tương đương 10%)',
        'type' => 'text',
        'value' => 0.1
    ],
    ConfigConstants::CONFIG_COMMISSION_FOUNDATION => [
        'title' => 'Tỉ lệ foundation',
        'hint' => 'Nhập số thập phân. vd: 0.05 (tương đương 5%)',
        'type' => 'text',
        'value' => 0.05
    ],
    ConfigConstants::CONFIG_COMMISSION_AUTHOR => [
        'title' => 'Tỉ lệ mặc định của tác giả',
        'hint' => 'Nhập số thập phân. vd: 0.2 (tương đương 20%)',
        'type' => 'text',
        'value' => 0.2
    ],
    ConfigConstants::CONFIG_COMMISSION_COMPANY => [
        'title' => 'Tỉ lệ lợi nhuận công ty',
        'hint' => 'Nhập số thập phân. vd: 0.45 (tương đương 45%)',
        'type' => 'text',
        'value' => 0.45
    ],
    ConfigConstants::CONFIG_BONUS_RATE => [
        'title' => 'Tỉ lệ chuyển tiền sử dụng về điểm thưởng',
        'hint' => 'vd: tỉ lệ 1000  thì tiêu 1000 vnd được 1 điểm',
        'type' => 'number',
        'value' => 1000
    ],
    ConfigConstants::CONFIG_TEACHER_BANNER => [
        'title' => 'Đường dẫn banner trang giảng viên',
        'hint' => 'Upload lên S3 và dán link vào',
        'type' => 'text',
        'value' => ''
    ],
    ConfigConstants::CONFIG_SCHOOL_BANNER => [
        'title' => 'Đường dẫn banner trang trung tâm',
        'hint' => 'Upload lên S3 và dán link vào',
        'type' => 'text',
        'value' => ''
    ],
];

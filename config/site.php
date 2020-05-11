<?php

use App\Constants\ConfigConstants;

return [
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
        ConfigConstants::CONFIG_FEE_TEACHER => [
            'title' => 'Phí hàng tháng của giản viên', 
            'hint' => '', 
            'type' => 'number', 
            'value' => 1000000
        ],
        ConfigConstants::CONFIG_FEE_SCHOOL => [
            'title' => 'Phí hàng tháng của trung tâm', 
            'hint' => '', 
            'type' => 'number', 
            'value' => 2000000
        ],
        ConfigConstants::CONFIG_COMMISSION => [
            'title' => 'Tỉ lệ commission', 
            'hint' => 'Nhập số thập phân. vd: 0.05 (tương đương 5%)', 
            'type' => 'text', 
            'value' => 0.05
        ],
        ConfigConstants::CONFIG_DISCOUNT => [
            'title' => 'Tỉ lệ chiết khấu mua hàng', 
            'hint' => 'Nhập số thập phân. vd: 0.1 (tương đương 10%)', 
            'type' => 'text', 
            'value' => 0.1
        ],
        ConfigConstants::CONFIG_BONUS_RATE => [
            'title' => 'Tỉ lệ chuyển tiền sử dụng về điểm thưởng', 
            'hint' => 'vd: tỉ lệ 1000  thì tiêu 1000 vnd được 1 điểm', 
            'type' => 'number', 
            'value' => 1000
        ],
    ];
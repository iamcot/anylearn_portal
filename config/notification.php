<?php

use App\Constants\NotifConstants;

return [
    NotifConstants::NEW_USER => [
        'title' => 'Chào mừng',
        'template' => 'Chào mừng {username} đến với anyLEARN. Vui lòng cập nhật thông tin',
        'route' => '/account',
    ],

    NotifConstants::NEW_FRIEND => [
        'title' => 'Bạn mới',
        'template' => '{friend} vừa mới tham gia anyLEARN với tư cách là bạn bè của bạn. Hãy chào mừng bạn ấy nào.',
        'route' => '/account/friends',
        'args' => true,
    ],
    NotifConstants::COURSE_APPROVE =>  [
        'title' => 'Khóa học được duyệt',
        'template' => 'Chúc mừng! Khóa học {course} của bạn đã được duyệt.',
    ],

    NotifConstants::REMIND_CONFIRM =>  [
        'title' => 'Hoàn thành khóa học!',
        'template' => 'Chúc mừng {username} đã vừa hoàn thành buổi học {course}. Hãy dành chút thời gian vào mục Lịch học bấm Xác nhận tham gia lớp học để phục vụ cho công tác điểm danh của buổi học nhé!',
        'route' => '/account/calendar'
    ],
];

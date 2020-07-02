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
];

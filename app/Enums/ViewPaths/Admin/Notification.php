<?php

namespace App\Enums\ViewPaths\Admin;

enum Notification
{
    const INDEX = [
        URI => '/',
        VIEW => 'admin-views.notification.index'
    ];

    const ADD = [
        URI => 'store',
        VIEW => 'admin-views.notification.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.notification.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const STATUS = [
        URI => 'status',
        VIEW => ''
    ];

    const EXPORT = [
        URI => 'export',
        VIEW => ''
    ];
}

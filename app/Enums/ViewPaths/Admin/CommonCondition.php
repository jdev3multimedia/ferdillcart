<?php

namespace App\Enums\ViewPaths\Admin;

enum CommonCondition
{
    const DROPDOWN = [
        URI => '/get-all',
        VIEW => ''
    ];

    const INDEX = [
        URI => '/',
        VIEW => 'admin-views.common-condition.index'
    ];

    const ADD = [
        URI => 'store',
        VIEW => 'admin-views.common-condition.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.common-condition.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const STATUS = [
        URI => 'status',
        VIEW => ''
    ];
}

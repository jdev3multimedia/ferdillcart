<?php

namespace App\Enums\ViewPaths\Admin;

enum Coupon
{
    const INDEX = [
        URI => '/',
        VIEW => 'admin-views.coupon.index'
    ];

    const ADD = [
        URI => 'store',
        VIEW => 'admin-views.coupon.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.coupon.edit'
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

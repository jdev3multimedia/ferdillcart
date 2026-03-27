<?php

namespace App\Enums\ViewPaths\Admin;

enum Brand
{
    const DROPDOWN = [
        URI => '/get-all',
        VIEW => ''
    ];

    const INDEX = [
        URI => '/',
        VIEW => 'admin-views.brand.index'
    ];

    const ADD = [
        URI => 'store',
        VIEW => 'admin-views.brand.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.brand.edit'
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

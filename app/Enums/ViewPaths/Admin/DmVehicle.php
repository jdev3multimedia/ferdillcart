<?php

namespace App\Enums\ViewPaths\Admin;

enum DmVehicle
{
    const INDEX = [
        URI => '/',
        VIEW => 'admin-views.dm-vehicle.list'
    ];

    const ADD = [
        URI => 'store',
        VIEW => 'admin-views.dm-vehicle.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.dm-vehicle.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const UPDATE_STATUS = [
        URI => 'status',
        VIEW => ''
    ];

    const VIEW = [
        URI => 'view',
        VIEW => 'admin-views.dm-vehicle.view'
    ];

}

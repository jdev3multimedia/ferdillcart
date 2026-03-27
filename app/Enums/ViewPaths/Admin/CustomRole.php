<?php

namespace App\Enums\ViewPaths\Admin;

enum CustomRole
{
    const ADD = [
        URI => 'create',
        VIEW => 'admin-views.custom-role.create'
    ];

    const EDIT = [
        URI => 'edit',
        VIEW => 'admin-views.custom-role.edit'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.custom-role.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];
    const SEARCH = [
        URI => 'search',
        VIEW => 'admin-views.custom-role.partials._table'
    ];
}

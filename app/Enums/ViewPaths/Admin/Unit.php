<?php

namespace App\Enums\ViewPaths\Admin;

enum Unit
{
    const INDEX = [
        URI => '/',
        VIEW => 'admin-views.unit.index'
    ];

    const ADD = [
        URI => 'store',
        VIEW => 'admin-views.unit.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.unit.edit'
    ];

    const SEARCH = [
        URI => 'search',
        VIEW => 'admin-views.unit.partials._table'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const EXPORT = [
        URI => 'export',
        VIEW => ''
    ];
}

<?php

namespace App\Enums\ViewPaths\Admin;

enum Addon
{
    const INDEX = [
        URI => '/',
        VIEW => 'admin-views.addon.index'
    ];

    const ADD = [
        URI => 'store',
        VIEW => 'admin-views.addon.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.addon.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const EXPORT = [
        URI => 'export',
        VIEW => ''
    ];

    const UPDATE_STATUS = [
        URI => 'status',
        VIEW => ''
    ];

    const BULK_IMPORT = [
        URI => 'bulk-import',
        VIEW => 'admin-views.addon.bulk-import'
    ];

    const BULK_UPDATE = [
        URI => 'bulk-update',
        VIEW => 'admin-views.addon.bulk-import'
    ];

    const BULK_EXPORT = [
        URI => 'bulk-export',
        VIEW => 'admin-views.addon.bulk-export'
    ];
}

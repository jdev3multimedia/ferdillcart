<?php

namespace App\Enums\ViewPaths\Admin;

enum Category
{
    const INDEX = [
        URI => 'view',
        VIEW => 'admin-views.category.index'
    ];

    const SUB_CATEGORY_INDEX = [
        URI => 'view',
        VIEW => 'admin-views.category.sub-index'
    ];

    const LIST = [
        URI => 'view',
        VIEW => 'admin-views.category.view'
    ];

    const NAME_LIST = [
        URI => 'get-all',
        VIEW => ''
    ];

    const ADD = [
        URI => 'add',
        VIEW => 'admin-views.category.index'
    ];

    const UPDATE = [
        URI => 'update',
        VIEW => 'admin-views.category.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const PRIORITY = [
        URI => 'update-priority',
        VIEW => ''
    ];

    const STATUS = [
        URI => 'status',
        VIEW => ''
    ];

    const FEATURED = [
        URI => 'featured',
        VIEW => ''
    ];

    const EXPORT = [
        URI => 'export-categories',
        VIEW => ''
    ];

    const BULK_IMPORT = [
        URI => 'bulk-import',
        VIEW => 'admin-views.category.bulk-import'
    ];

    const BULK_UPDATE = [
        URI => 'bulk-update',
        VIEW => ''
    ];

    const BULK_EXPORT = [
        URI => 'bulk-export',
        VIEW => 'admin-views.category.bulk-export'
    ];

}

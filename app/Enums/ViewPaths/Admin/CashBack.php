<?php

namespace App\Enums\ViewPaths\Admin;

enum CashBack
{
    const INDEX = [
        URI => '/',
        VIEW => 'admin-views.promotions.cashback.index'
    ];

    const ADD = [
        URI => 'store',
        VIEW => 'admin-views.promotions.cashback.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.promotions.cashback.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const UPDATE_STATUS = [
        URI => 'status',
        VIEW => ''
    ];

    // const SEARCH = [
    //     URI => 'search',
    //     VIEW => 'admin-views.promotions.cashback.partials._table'
    // ];

}

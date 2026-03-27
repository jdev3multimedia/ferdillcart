<?php

namespace App\Enums\ViewPaths\Admin;

enum Zone
{
    const INDEX = [
        URI => '/',
        VIEW => 'admin-views.zone.index'
    ];

    const ADD = [
        URI => 'store',
        VIEW => 'admin-views.zone.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.zone.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const EXPORT = [
        URI => 'export',
        VIEW => ''
    ];

    const LATEST_MODULE_SETUP = [
        URI => 'module-setup',
        VIEW => 'admin-views.zone.module-setup'
    ];

    const MODULE_SETUP = [
        URI => 'module-setup',
        VIEW => 'admin-views.zone.module-setup'
    ];

    const SURGE_SETUP = [
        URI => 'surge-setup',
        VIEW => 'admin-views.zone.surge-setup'
    ];

    const STATUS = [
        URI => 'status',
        VIEW => ''
    ];

    const DIGITAL_PAYMENT = [
        URI => 'digital-payment',
        VIEW => ''
    ];

    const CASH_ON_DELIVERY = [
        URI => 'cash-on-delivery',
        VIEW => ''
    ];

    const OFFLINE_PAYMENT = [
        URI => 'offline-payment',
        VIEW => ''
    ];

    const INSTRUCTION = [
        URI => 'instruction',
        VIEW => ''
    ];

    const ZONE_FILTER = [
        URI => 'zone-filter',
        VIEW => ''
    ];

    const MODULE_UPDATE = [
        URI => 'module-update',
        VIEW => ''
    ];

    const GET_COORDINATES = [
        URI => 'zone/get-coordinates',
        VIEW => ''
    ];

    const GET_ZONE = [
        URI => 'zone/get-zone',
        VIEW => ''
    ];

    const GET_ALL_ZONE_COORDINATES = [
        URI => 'get-all-zone-coordinates',
        VIEW => ''
    ];
}

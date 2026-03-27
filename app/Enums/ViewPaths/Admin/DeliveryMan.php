<?php

namespace App\Enums\ViewPaths\Admin;

enum DeliveryMan
{
    const LIST = [
        URI => '/',
        VIEW => 'admin-views.delivery-man.list'
    ];

    const NEW = [
        URI => 'new',
        VIEW => 'admin-views.delivery-man.new'
    ];

    const DENY = [
        URI => 'deny',
        VIEW => 'admin-views.delivery-man.deny'
    ];

    const ADD = [
        URI => 'add',
        VIEW => 'admin-views.delivery-man.index'
    ];

    const UPDATE = [
        URI => 'edit',
        VIEW => 'admin-views.delivery-man.edit'
    ];

    const DELETE = [
        URI => 'delete',
        VIEW => ''
    ];

    const STATUS = [
        URI => 'status',
        VIEW => ''
    ];

    const EARNING = [
        URI => 'earning',
        VIEW => ''
    ];

    const UPDATE_APPLICATION = [
        URI => 'update-application',
        VIEW => ''
    ];

    const EXPORT = [
        URI => 'export',
        VIEW => ''
    ];

    const EARNING_EXPORT = [
        URI => 'earning-export',
        VIEW => ''
    ];

    const REVIEW_EXPORT = [
        URI => 'review-export',
        VIEW => ''
    ];

    const LOYALTY_POINT_EXPORT = [
        URI => 'loyalty-point-export',
        VIEW => ''
    ];

    const REFERRAL_EXPORT = [
        URI => 'referral-export',
        VIEW => ''
    ];

    const ACTIVE_SEARCH = [
        URI => 'active-search',
        VIEW => ''
    ];

    const PREVIEW = [
        URI => 'preview',
        VIEW => 'admin-views.delivery-man.view'
    ];

    const SEARCH = [
        URI => 'search',
        VIEW => 'admin-views.delivery-man.partials._table'
    ];
    const REVIEW_LIST = [
        URI => '',
        VIEW => 'admin-views.delivery-man.reviews-list'
    ];

    const REVIEW_SEARCH_LIST = [
        URI => 'search',
        VIEW => 'admin-views.delivery-man.partials._review'
    ];

    const REVIEW_STATUS = [
        URI => 'status',
        VIEW => ''
    ];

    const INFO = [
        URI => 'search',
        VIEW => 'admin-views.delivery-man.view.info'
    ];

    const TRANSACTION = [
        URI => 'search',
        VIEW => 'admin-views.delivery-man.view.transaction'
    ];

    const CONVERSATION = [
        URI => 'search',
        VIEW => 'admin-views.delivery-man.view.conversations'
    ];

    const CONVERSATION_LIST = [
        URI => 'search',
        VIEW => 'admin-views.delivery-man.partials._conversation_list'
    ];

    const CONVERSATIONS = [
        URI => 'search',
        VIEW => 'admin-views.delivery-man.partials._conversations'
    ];
    const DROPDOWN_LIST = [
        URI => 'get-deliverymen',
        VIEW => ''
    ];
    const ACCOUNT_DATA = [
        URI => 'get-account-data',
        VIEW => ''
    ];

    const CONVERSATION_VIEW = [
        URI => 'message',
        VIEW => ''
    ];

    const CONVERSATION_DETAILS = [
        URI => 'message/details',
        VIEW => ''
    ];
    const ORDER_LIST = [
        URI => 'search',
        VIEW => 'admin-views.delivery-man.view.order_list'
    ];

}

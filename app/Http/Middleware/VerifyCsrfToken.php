<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/external-login-from-drivemond','/api/v1/customer/external-update-data',
        '/api/v1/get-customer','/payment*','/pay-via-ajax', '/success','/cancel','/fail','/ipn','/payment-razor/*','/paytm-response','/liqpay-callback','/paytm-response','/mercadopago/make-payment','/flutterwave-pay','/paytabs-response','/vendor-panel/item/food-variation-generate','/vendor-panel/item/variation-generate'
    ];
}

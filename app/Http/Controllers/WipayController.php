<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Traits\Processor;

class WipayController extends Controller
{
    use Processor;

    private $config_values;
    private PaymentRequest $payment;
    private $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('wipay', 'payment_config');

        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }

        $this->payment = $payment;
        $this->user = $user;
    }

    /**
     * CURL FUNCTION
     */
    protected function cURL($url, $data)
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            Log::error('WiPay Curl Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        return json_decode($response);
    }

    /**
     * INITIATE PAYMENT
     */
    public function pay(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json(
                $this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)),
                400
            );
        }

        $data = $this->payment::where([
            'id' => $request['payment_id'],
            'is_paid' => 0
        ])->first();

        if (!$data) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }

        $payer = json_decode($data->payer_information);

        /**
         * WIPAY PAYLOAD
         */
        $payload = [
            "account_number" => $this->config_values->account_number ?? "1234567890",
            "country_code"   => $this->config_values->country_code ?? "TT",
            "currency"       => $this->config_values->currency ?? "TTD",
            "environment"    => $this->config_values->environment ?? "sandbox",
            "fee_structure"  => "customer_pay",
            "method"         => "credit_card",
            "order_id"       => $data->id,
            "origin"         => "your_app",

            // ✅ IMPORTANT FIX
            "response_url"   => route('wipay.callback', ['payment_id' => $data->id]),

            "total"          => number_format($data->payment_amount, 2, '.', ''),

            "email"          => $payer->email ?? '',
            "name"           => $payer->name ?? '',
            "phone"          => $payer->phone ?? '',
        ];

        try {
            $response = $this->cURL(
                "https://tt.wipayfinancial.com/plugins/payments/request",
                $payload
            );
        } catch (\Exception $e) {
            Log::error('WiPay Exception: ' . $e->getMessage());
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_404), 200);
        }

        if (!$response || !isset($response->url)) {
            Log::error('WiPay Invalid Response', (array) $response);
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_500), 500);
        }

        return Redirect::away($response->url);
    }

    /**
     * CALLBACK HANDLER
     */
   public function callback(Request $request)
    {
        $payment_id = $request->get('payment_id');
        $status = $request->get('status');
        $transaction_id = $request->get('transaction_id');

        $payment_data = $this->payment::where('id', $payment_id)->first();

        if (!$payment_data) {
            return response()->json(['error' => 'Invalid payment'], 400);
        }

        if ($status === "success") {
            $this->payment::where('id', $payment_id)->update([
                'payment_method' => 'wipay',
                'is_paid' => 1,
                'transaction_id' => $transaction_id ?? $payment_id,
            ]);

            $payment_data = $this->payment::where('id', $payment_id)->first();
            
            if (isset($payment_data->success_hook) && function_exists($payment_data->success_hook)) {
                call_user_func($payment_data->success_hook, $payment_data);
            }
            
            return $this->payment_response($payment_data, 'success');
        }

        if (isset($payment_data->failure_hook) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }

        return $this->payment_response($payment_data, 'fail');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\PaymentRequest;
use App\Models\User;
use App\Traits\Processor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoController extends Controller
{
    use Processor;

    private PaymentRequest $paymentRequest;
    private $config;
    private $user;

    public function __construct(PaymentRequest $paymentRequest, User $user)
    {
        $config = $this->payment_config('mercadopago', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config = json_decode($config->test_values);
        }
        $this->paymentRequest = $paymentRequest;
        $this->user = $user;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->paymentRequest::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }
        $config = $this->config;
        return view('payment-views.payment-view-marcedo-pogo', compact('config', 'data'));
    }

    public function make_payment(Request $request)
    {
        MercadoPagoConfig::setAccessToken($this->config->access_token);
        $requestOptions = new RequestOptions();
        $requestOptions->setCustomHeaders([
            "x-idempotency-key" => (string)uniqid("mp_", true),
        ]);
        $paymentRequest = $this->paymentRequest->where('id', $request['payment_id'])->first();
        $client = new PaymentClient();

        try {
            $payment = $client->create([
                "token" => $request['token'],
                "issuer_id" => $request['issuer_id'] ?? null,
                "payment_method_id" => $request['payment_method_id'],
                "transaction_amount" => (float)$request['transaction_amount'],
                "installments" => (int)($request['installments'] ?? 1),
                "external_reference" => $paymentRequest->id, // important!
                "payer" => [
                    "email" => $request['payer']['email'],
                    "identification" => [
                        "type" => $request['payer']['identification']['type'],
                        "number" => $request['payer']['identification']['number']
                    ]
                ]
            ], $requestOptions);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'fail',
                'message' => $e->getMessage(),
                'error' => $e
            ], 500);
        }

        if ($payment->status == 'approved') {
            $this->paymentRequest::where(['id' => $paymentRequest->id])->update([
                'payment_method' => 'mercadopago',
                'is_paid' => 1,
                'transaction_id' => $payment->id,
            ]);
            $data = $this->paymentRequest::where(['id' => $request['payment_id']])->first();
            if (isset($data) && function_exists($data->success_hook)) {
                call_user_func($data->success_hook, $data);
            }
            return response()->json(['status' => 'success']);
        }
        return response()->json(['status' => 'fail']);
    }

    public function callback(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
        if ($request['status'] == 'success') {
            $data = $this->paymentRequest::where(['id' => $request['payment_id']])->first();
            return $this->payment_response($data,'success');
        }
        $payment_data = $this->paymentRequest::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data,'fail');
    }
}

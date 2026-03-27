<?php


namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use App\Models\PaymentRequest;
use App\Traits\Processor;
use Exception;

class PaytmController extends Controller
{
    use Processor;

    private mixed $config_values;
	private  static $iv = "@@@@&&&&####$$$$";
    private PaymentRequest $payment;
    private User $user;

    public function __construct(PaymentRequest $payment, User $user)
    {
        $config = $this->payment_config('paytm', 'payment_config');
        if (!is_null($config) && $config->mode == 'live') {
            $this->config_values = json_decode($config->live_values);
        } elseif (!is_null($config) && $config->mode == 'test') {
            $this->config_values = json_decode($config->test_values);
        }
        if (isset($config)) {

            $PAYTM_STATUS_QUERY_NEW_URL = 'https://securestage.paytmpayments.com/theia/api/v1/showPaymentPage';
            $PAYTM_TXN_URL = 'https://securestage.paytmpayments.com/theia/api/v1/initiateTransaction';
            if ($config->mode == 'live') {
                $PAYTM_STATUS_QUERY_NEW_URL = 'https://secure.paytmpayments.com/theia/api/v1/showPaymentPage';
                $PAYTM_TXN_URL = 'https://secure.paytmpayments.com/theia/api/v1/initiateTransaction';
            }

            $config = array(
                'PAYTM_ENVIRONMENT' => ($config->mode == 'test') ? 'TEST' : 'PROD',
                'PAYTM_MERCHANT_KEY' => env('PAYTM_MERCHANT_KEY', $this->config_values->merchant_key),
                'PAYTM_MERCHANT_MID' => env('PAYTM_MERCHANT_MID', $this->config_values->merchant_id),
                'PAYTM_MERCHANT_WEBSITE' => env('PAYTM_MERCHANT_WEBSITE', $this->config_values->merchant_website_link),
                'PAYTM_REFUND_URL' => env('PAYTM_REFUND_URL', $this->config_values->refund_url ?? ''),
                'PAYTM_STATUS_QUERY_URL' => env('PAYTM_STATUS_QUERY_URL', $PAYTM_STATUS_QUERY_NEW_URL),
                'PAYTM_STATUS_QUERY_NEW_URL' => env('PAYTM_STATUS_QUERY_NEW_URL', $PAYTM_STATUS_QUERY_NEW_URL),
                'PAYTM_TXN_URL' => env('PAYTM_TXN_URL', $PAYTM_TXN_URL),
            );

            Config::set('paytm_config', $config);
        }
        $this->payment = $payment;
        $this->user = $user;
    }

	 public function encrypt($input, $key) {
		$key = html_entity_decode($key);

		if (function_exists('openssl_encrypt')) {
			$data = openssl_encrypt($input, "AES-128-CBC", $key, 0, self::$iv);
		} else {
			throw new Exception('OpenSSL extension is not available. Please install the OpenSSL extension.');
		}
		return $data;
	}

	 public function decrypt($encrypted, $key) {
		$key = html_entity_decode($key);

		if(function_exists('openssl_decrypt')){
			$data = openssl_decrypt ( $encrypted , "AES-128-CBC" , $key, 0, self::$iv );
		} else {
			throw new Exception('OpenSSL extension is not available. Please install the OpenSSL extension.');
		}
		return $data;
	}

	 public function generateSignature($params, $key) {
		if(!is_array($params) && !is_string($params)){
			throw new Exception("string or array expected, ".gettype($params)." given");
		}
		if(is_array($params)){
			$params = self::getStringByParams($params);
		}
		return self::generateSignatureByString($params, $key);
	}

	 public function verifySignature($params, $key, $checksum){
		if(!is_array($params) && !is_string($params)){
			throw new Exception("string or array expected, ".gettype($params)." given");
		}
		if(isset($params['CHECKSUMHASH'])){
			unset($params['CHECKSUMHASH']);
		}
		if(is_array($params)){
			$params = self::getStringByParams($params);
		}
		return self::verifySignatureByString($params, $key, $checksum);
	}

	 private function generateSignatureByString($params, $key){
		$salt = self::generateRandomString(4);
		return self::calculateChecksum($params, $key, $salt);
	}

	 private function verifySignatureByString($params, $key, $checksum){
		$paytm_hash = self::decrypt($checksum, $key);
		$salt = substr($paytm_hash, -4);
		return $paytm_hash == self::calculateHash($params, $salt) ? true : false;
	}

	 private function generateRandomString($length) {
		$random = "";
		$data = "9876543210ZYXWVUTSRQPONMLKJIHGFEDCBAabcdefghijklmnopqrstuvwxyz!@#$&_";

		for ($i = 0; $i < $length; $i++) {
			$random .= substr($data, (rand() % (strlen($data))), 1);
		}

		return $random;
	}

	 private function getStringByParams($params) {
		ksort($params);
		$params = array_map(function ($value){
			return ($value !== null && strtolower($value) !== "null") ? $value : "";
	  	}, $params);
		return implode("|", $params);
	}

	 private function calculateHash($params, $salt){
		$finalString = $params . "|" . $salt;
		$hash = hash("sha256", $finalString);
		return $hash . $salt;
	}

	 private function calculateChecksum($params, $key, $salt){
		$hashString = self::calculateHash($params, $salt);
		return self::encrypt($hashString, $key);
	}

	 private function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	 private function pkcs5Unpad($text) {
		$pad = ord($text[strlen($text) - 1]);
		if ($pad > strlen($text))
			return false;
		return substr($text, 0, -1 * $pad);
	}


    public function payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|uuid'
        ]);

        if ($validator->fails()) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_400, null, $this->error_processor($validator)), 400);
        }

        $data = $this->payment::where(['id' => $request['payment_id']])->where(['is_paid' => 0])->first();
        if (!isset($data)) {
            return response()->json($this->response_formatter(GATEWAYS_DEFAULT_204), 200);
        }
        $payer = json_decode($data['payer_information']);
        $ORDER_ID = time();


            $paytmParams = array();

            $paytmParams["body"] = array(
            "requestType" => "Payment",
            "mid"      => Config::get('paytm_config.PAYTM_MERCHANT_MID'),
            "websiteName"  => Config::get('paytm_config.PAYTM_MERCHANT_WEBSITE'),
            "orderId"    => $ORDER_ID,
            "callbackUrl"  => route('paytm.response', ['payment_id' => $data->id]),
            "txnAmount"   => array(
                "value"   => (string) round($data->payment_amount, 2),
                "currency" => "INR",
            ),
            "userInfo"   => array(
                "custId"  => $data['payer_id'],
            ),
            );

         
            $checksum = self::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), Config::get('paytm_config.PAYTM_MERCHANT_KEY'));

            $paytmParams["head"] = array(
                "signature" => $checksum
            );

            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);


            $url = Config::get('paytm_config.PAYTM_TXN_URL')
                . "?mid=" . Config::get('paytm_config.PAYTM_MERCHANT_MID')
                . "&orderId=" . $ORDER_ID;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
            $response = curl_exec($ch);


            $data = json_decode($response, true);

            $resultStatus = $data['body']['resultInfo']['resultStatus'] ?? null;
            $resultMsg    = $data['body']['resultInfo']['resultMsg'] ?? null;
            $txnToken     = $data['body']['txnToken'] ?? null;


        return view('payment-views.paytm', compact('txnToken','ORDER_ID'));
    }

    public function callback(Request $request): JsonResponse|Redirector|RedirectResponse|Application
    {
            if ($request["STATUS"] == "TXN_SUCCESS") {
                $this->payment::where(['id' => $request['payment_id']])->update([
                    'payment_method' => 'paytm',
                    'is_paid' => 1,
                    'transaction_id' => $request['TXNID'],
                ]);

                $data = $this->payment::where(['id' => $request['payment_id']])->first();

                if (isset($data) && function_exists($data->success_hook)) {
                    call_user_func($data->success_hook, $data);
                }
                return $this->payment_response($data, 'success');
            }

        $payment_data = $this->payment::where(['id' => $request['payment_id']])->first();
        if (isset($payment_data) && function_exists($payment_data->failure_hook)) {
            call_user_func($payment_data->failure_hook, $payment_data);
        }
        return $this->payment_response($payment_data, 'fail');
    }
}

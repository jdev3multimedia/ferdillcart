<?php

namespace App\Traits;

use App\Models\BusinessSetting;
use Illuminate\Support\Facades\Http;

trait NotificationTrait
{
    public static function sendPushNotificationToTopic($data, $topic, $type,$web_push_link = null): bool|string
    {
        if(isset($data['module_id'])){
            $module_id = $data['module_id'];
        }else{
            $module_id = '';
        }
        if(isset($data['order_type'])){
            $order_type = $data['order_type'];
        }else{
            $order_type = '';
        }
        if(isset($data['zone_id'])){
            $zone_id = $data['zone_id'];
        }else{
            $zone_id = '';
        }

//        $click_action = "";
//        if($web_push_link){
//            $click_action = ',
//            "click_action": "'.$web_push_link.'"';
//        }

        if (isset($data['order_id'])) {
            $postData = [
                'message' => [
                    "topic" => $topic,
                    "data" => [
                        "title" => (string)$data['title'],
                        "body" => (string)$data['description'],
                        "order_id" => (string)$data['order_id'],
                        "order_type" => (string)$order_type,
                        "type" => (string)$type,
                        "image" => (string)$data['image'],
                        "module_id" => (string)$module_id,
                        "zone_id" => (string)$zone_id,
                        "title_loc_key" => (string)$data['order_id'],
                        "body_loc_key" => (string)$type,
                        "click_action" => $web_push_link?(string)$web_push_link:'',
                        "sound" => "notification.wav",
                    ],
                    "notification" => [
                        "title" => (string)$data['title'],
                        "body" => (string)$data['description'],
                        "image" => (string)$data['image'],
                    ],
                    "android" => [
                        "notification" => [
                            "channelId" => '6ammart',
                        ]
                    ],
                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "sound" => "notification.wav"
                            ]
                        ]
                    ],
                ]
            ];
        } else {
            $postData = [
                'message' => [
                    "topic" => $topic,
                    "data" => [
                        "title" => (string)$data['title'],
                        "body" => (string)$data['description'],
                        "type" => (string)$type,
                        "image" => (string)$data['image'],
                        "body_loc_key" => (string)$type,
                        "click_action" => $web_push_link?(string)$web_push_link:'',
                        "sound" => "notification.wav",
                    ],
                    "notification" => [
                        "title" => (string)$data['title'],
                        "body" => (string)$data['description'],
                        "image" => (string)$data['image'],
                    ],
                    "android" => [
                        "notification" => [
                            "channelId" => '6ammart',
                        ]
                    ],
                    "apns" => [
                        "payload" => [
                            "aps" => [
                                "sound" => "notification.wav"
                            ]
                        ]
                    ],
                ]
            ];
        }
        return self::sendNotificationToHttp($postData);
    }

    public static function sendPushNotificationToDevice($fcm_token, $data, $web_push_link = null): bool|string
    {
        //        if(isset($data['message'])){
//            $message = $data['message'];
//        }else{
//            $message = '';
//        }
        if(isset($data['conversation_id'])){
            $conversation_id = $data['conversation_id'];
        }else{
            $conversation_id = '';
        }
        if(isset($data['sender_type'])){
            $sender_type = $data['sender_type'];
        }else{
            $sender_type = '';
        }
        if(isset($data['module_id'])){
            $module_id = $data['module_id'];
        }else{
            $module_id = '';
        }
        if(isset($data['order_type'])){
            $order_type = $data['order_type'];
        }else{
            $order_type = '';
        }

//        $click_action = "";
//        if($web_push_link){
//            $click_action = ',
//            "click_action": "'.$web_push_link.'"';
//        }
        $postData = [
            'message' => [
                "token" => $fcm_token,
                "data" => [
                    "title" => (string)$data['title'],
                    "body" => (string)$data['description'],
                    "image" => (string)$data['image'],
                    "order_id" => (string)$data['order_id'],
                    "type" => (string)$data['type'],
                    "conversation_id" => (string)$conversation_id,
                    "module_id" => (string)$module_id,
                    "sender_type" => (string)$sender_type,
                    "order_type" => (string)$order_type,
                    "click_action" => $web_push_link?(string)$web_push_link:'',
                    "sound" => "notification.wav",
                ],
                "notification" => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    "image" => (string)$data['image'],
                ],
                "android" => [
                    "notification" => [
                        "channelId" => '6ammart',
                    ]
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => "notification.wav"
                        ]
                    ]
                ]
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function sendNotificationToHttp(array|null $data)
    {
        $config = self::get_business_settings('push_notification_service_file_content');
        $key = (array)$config;
        if($key['project_id']){
            $url = 'https://fcm.googleapis.com/v1/projects/'.$key['project_id'].'/messages:send';
            $headers = [
                'Authorization' => 'Bearer ' . self::getAccessToken($key),
                'Content-Type' => 'application/json',
            ];
            try {
                Http::withHeaders($headers)->post($url, $data);
            }catch (\Exception $exception){
                return false;
            }
        }
        return false;
    }

    public static function getAccessToken($key)
    {
        $jwtToken = [
            'iss' => $key['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => time() + 3600,
            'iat' => time(),
        ];
        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtPayload = base64_encode(json_encode($jwtToken));
        $unsignedJwt = $jwtHeader . '.' . $jwtPayload;
        openssl_sign($unsignedJwt, $signature, $key['private_key'], OPENSSL_ALGO_SHA256);
        $jwt = $unsignedJwt . '.' . base64_encode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);
        return $response->json('access_token');
    }

    public static function get_business_settings($name)
    {
        $config = null;

        $paymentmethod = BusinessSetting::where('key', $name)->first();

        if ($paymentmethod) {
            $config = json_decode($paymentmethod->value, true);
        }

        return $config;
    }
}

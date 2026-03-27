<?php

namespace App\Listeners;

use App\Jobs\DispatchDriverLocationJob;
use App\Models\DeliveryHistory;
use App\Models\DeliveryMan;
use Laravel\Reverb\Events\MessageReceived;

class HandleClientMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageReceived $event): void
    {
        $message = json_decode($event->message, true);

        if (isset($message['event']) && str_starts_with($message['event'], 'client-')) {
            $channel = $message['channel'] ?? null;
            $eventName = $message['event'] ?? null;
            $data = $message['data'] ?? [];
            if ($eventName === 'client-location-update' && str_starts_with($channel, 'private-user-location')) {
                $token = $data['token'] ?? null;
                $latitude = $data['latitude'] ?? null;
                $longitude = $data['longitude'] ?? null;

                $deliverymanId = DeliveryMan::where(['auth_token' => $token])->first()?->id;

                if ($deliverymanId && $latitude && $longitude) {
                    DeliveryHistory::updateOrCreate(['delivery_man_id' => $deliverymanId], [
                        'longitude' => $data['longitude'],
                        'latitude' => $data['latitude'],
                        'time' => now(),
                        'location' => $data['location'],
                    ]);
                    try {
                        dispatch(new DispatchDriverLocationJob($deliverymanId, $latitude, $longitude, $data['location']))->onQueue('default');
                    } catch (\Exception $e) {
                        info($e->getMessage());
                    }
                }
            }
        }
    }
}

<?php

namespace App\Events;


use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $deliverymanId;

    public $latitude;

    public $longitude;

    public $location;

    public function __construct($deliverymanId, $latitude, $longitude, $location)
    {
        $this->deliverymanId = $deliverymanId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->location = $location;
        info("Broadcasting location update for deliveryman ID: {$deliverymanId} to dm_location_{$deliverymanId} channel.");
    }

    public function broadcastOn(): array 
    {
        return [
            new Channel('dm_location_'.$this->deliverymanId),
        ];
    }
    
    public function broadcastAs(): string
    {
        return 'dm_location_'.$this->deliverymanId;
    }

    public function broadcastWith(): array
    {
    
        return [
            'deliveryman_id' => $this->deliverymanId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location' => $this->location,
        ];
    }
}
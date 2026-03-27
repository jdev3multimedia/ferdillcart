<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ReportFilter;

class DeliverymanLoyaltyPointHistory extends Model
{
    use ReportFilter;
    protected $casts = [
        'deliveryman_id' => 'integer',
        'point' => 'integer',
        'converted_amount' => 'float',
    ];

    public function deliveryman()
    {
        return $this->belongsTo(Deliveryman::class);
    }

}

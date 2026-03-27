<?php

namespace Modules\TaxModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemTaxSetup extends Model
{
    use HasFactory;

     use HasFactory;

    protected $guarded = ['id'];

  protected $casts = [
        'is_default' => 'integer',
        'is_active' => 'integer',
        'is_included' => 'integer',
        'tax_ids' => 'array',
    ];

     public function additionalData(): HasMany
    {
        return $this->hasMany(TaxAdditionalSetup::class, 'system_tax_setup_id');
    }
}

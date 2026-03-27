<?php

namespace Modules\TaxModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Taxable extends Model
{
    use HasFactory;

    protected $table = 'taxables';

    protected $guarded = ['id'];
    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function taxable()
    {
        return $this->morphTo();
    }
}

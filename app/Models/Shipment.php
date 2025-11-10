<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'carrier',
        'tracking_number',
        'pickup_id',
        'pickup_name',
        'pickup_address',
        'pickup_city',
        'pickup_postal_code',
        'pickup_country',
        'label_pdf_url',
        'status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
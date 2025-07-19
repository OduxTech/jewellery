<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerialNumber extends Model
{
    protected $table = 'product_serials';

    protected $fillable = [
        'product_id',
        'variation_id',
        'purchase_line_id',
        'transaction_id',
        'serial_number',
        'status',
        'business_id',
        'location_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

   
}


<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldRate extends Model
{
    use HasFactory;

    protected $table = 'gold_rates';

    // Fillable fields
    protected $fillable = [
        'type',
        'price',
        'date',
        'created_by',
    ];

    // Optionally, you can cast fields (for safety and convenience)
    protected $casts = [
        'price' => 'float',
        'date' => 'date',
    ];
}
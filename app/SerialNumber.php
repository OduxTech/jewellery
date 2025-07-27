<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerialNumber extends Model
{
    protected $guarded = ['id'];
    protected $table = 'product_serials';

    protected $fillable = [
        'product_id',
        'variation_id',
        'purchase_line_id',
        'purchase_transaction_id',
        'serial_number',
        'status',
        'transaction_sell_lines_id',
        'sell_transaction_id',
        'business_id',
        'location_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    public function product()
    {
        return $this->belongsTo(\App\Product::class, 'product_id');
    }

    public function variation()
    {
        return $this->belongsTo(\App\Variation::class);
    }

    public function purchaseLine()
    {
        return $this->belongsTo(\App\PurchaseLine::class);
    }

    public function purchaseTransaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'purchase_transaction_id');
    }

    public function sellLine()
    {
        return $this->belongsTo(\App\TransactionSellLine::class, 'transaction_sell_lines_id');
    }

    public function sellTransaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'sell_transaction_id');
    }

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }
}

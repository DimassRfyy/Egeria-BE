<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $fillable = [
        'booking_transaction_id',
        'cosmetic_id',
        'quantity',
        'price',
    ];

    public function bookingTransaction()
    {
        return $this->belongsTo(BookingTransaction::class);
    }

    public function cosmetic()
    {
        return $this->belongsTo(Cosmetic::class);
    }
}

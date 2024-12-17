<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public static function getTopCosmetics()
    {
        return self::select('cosmetic_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('bookingTransaction', function ($query) {
                $query->where('is_paid', true);
            })
            ->groupBy('cosmetic_id')
            ->orderBy('total_quantity', 'desc')
            ->with('cosmetic:id,name') // Assuming the Cosmetic model has 'id' and 'name' columns
            ->take(10)
            ->get();
    }
}

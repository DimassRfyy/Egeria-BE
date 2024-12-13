<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingTransaction extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'trx_id',
        'proof',
        'total_amount',
        'total_tax_amount',
        'is_paid',
        'address',
        'city',
        'post_code',
        'sub_total_amount',
        'quantity',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public static function generateUniqueTrxId(){
        $prefix = 'EGR';
        do {
            $randomString = $prefix . mt_rand(10000, 99999);
        } while (self::where('trx_id', $randomString)->exists());

        return $randomString;
    }
}

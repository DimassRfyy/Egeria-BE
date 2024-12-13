<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CosmeticBenefit extends Model
{
    protected $fillable = ['name','cosmetic_id'];

    public function cosmetic()
    {
        return $this->belongsTo(Cosmetic::class);
    }
}

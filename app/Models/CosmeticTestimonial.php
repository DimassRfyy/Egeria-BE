<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CosmeticTestimonial extends Model
{
    protected $fillable = ['name', 'message', 'photo', 'rating', 'cosmetic_id'];

    public function cosmetic()
    {
        return $this->belongsTo(Cosmetic::class);
    }
}

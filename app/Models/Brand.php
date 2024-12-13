<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'photo'];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function cosmetics()
    {
        return $this->hasMany(Cosmetic::class);
    }

    public function popularCosmetics()
    {
        return $this->cosmetics()->where('is_popular', true)->orderBy('created_at', 'desc');
    }
}

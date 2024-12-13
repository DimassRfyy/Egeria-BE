<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cosmetic extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
        'about',
        'category_id',
        'brand_id',
        'is_popular',
        'price',
        'stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function benefits()
    {
        return $this->hasMany(CosmeticBenefit::class);
    }

    public function photos()
    {
        return $this->hasMany(CosmeticPhoto::class);
    }

    public function testimonials()
    {
        return $this->hasMany(CosmeticTestimonial::class);
    }

    public function transactions()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}

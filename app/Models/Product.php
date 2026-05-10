<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'price',
        'sale_price',
        'quantity',
        'status',
        'image',
        'description',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'category_name',
        'slug_category',
        'status',
        'image',
        'description',
    ];
    protected $casts = [
        'status' => 'string',
    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}

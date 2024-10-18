<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description",
        "price",
        "stock_quantity",
        "category"
    ];

    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity', 'price_at_time')
            ->withTimestamps();
    }
}

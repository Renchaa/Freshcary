<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    
    protected $table = "cart";
    protected $fillable = [
        "name",
        "image",
        "price",
        "qty",
        "pro_id",
        "user_id",
        "subtotal"
    ];
}

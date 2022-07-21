<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart';
    protected $fillable = ['id', 'cid', 'cart', 'checkout', 'coupon', 'abandon_status', 'created_at', 'updated_at'];
}

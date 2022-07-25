<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $table = 'shipping_address';
    protected $primaryKey = 'address_id';
    protected $fillable = ['address_id', 'customer_id', 'name', 'mobile', 'address1', 'address2', 'city', 'state', 'country', 'pin', 'status', 'address_location', 'default_address', 'custom_fields', 'created_at', 'updated_at'];
}

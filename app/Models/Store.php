<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = 'store_table';
    protected $primaryKey = 'store_id';
    protected $fillable = ['store_id', 'store_name', 'auth_code', 'access_token', 'refresh_access_token','token_created_at','facebook_access_token','long_lived_facebook_access_token','facebook_token_expires_in'];
}

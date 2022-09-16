<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatUsersLists extends Model
{
    use HasFactory;
    protected $table = 'chat_users_list';
    protected $fillable = ['store_id', 'customer_id', 'customer_mobile', 'last_message','created_at','updated_at'];

    public function userDetails()
    {
        return $this->hasOne(\App\Models\Customer::class, 'customer_id', 'customer_id');
    }
}

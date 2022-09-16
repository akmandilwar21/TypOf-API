<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChatHistory extends Model
{
    use HasFactory;
    protected $table = 'user_chat_history';
    protected $fillable = ['store_id', 'customer_id', 'customer_mobile', 'message', 'last_message','sender','recipient','created_at','updated_at'];
}

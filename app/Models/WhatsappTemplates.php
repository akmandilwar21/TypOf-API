<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappTemplates extends Model
{
    use HasFactory;
    protected $table = 'whatsapp_templates';
    protected $fillable = ['id', 'template_name', 'template_body', 'created_at'];
}

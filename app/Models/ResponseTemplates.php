<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseTemplates extends BaseModel
{
    use HasFactory;
 protected $table = 'response_templates';
    protected $fillable = [
        'name',
        'message',
    ];
}

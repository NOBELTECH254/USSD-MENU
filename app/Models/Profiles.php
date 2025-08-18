<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profiles extends BaseModel
{
    
    use HasFactory;
    protected $fillable = [
        'mobile_number',
        'first_name',
        'last_name',
        'display_name',
        'hashed_pin',
        'customer_id',
        'status',
        'last_dial_at',
        'status_history'

    ];
    protected $casts = [
        'status_history' => 'array',
    ];
    public function profileSettings()
    {
    }
}

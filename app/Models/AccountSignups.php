<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountSignups extends BaseModel
{
    use HasFactory;
 protected $table = 'account_signups';
    protected $fillable = [
        'mobile_number',
        'customer_name',
        'terms_approved',
        'status',
        "year_of_birth",
        "national_id"
        ];
}

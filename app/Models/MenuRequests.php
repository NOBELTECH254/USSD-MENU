<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  Illuminate\Support\Str;
use Carbon\Carbon;

class MenuRequests extends BaseModel
{
    use HasFactory;
    use HasFactory;
    protected $table  = 'menu_requests';
    protected $primaryKey = 'id';
    protected $fillable = [
'mobile_number',
"menu",
"request",
"response",
"status",
"created_at",
"request_data",
"request_response"
    ];
    protected $casts = [
        'request_data' => 'array',        'request_response' => 'array',

    ];
   protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
           // $model->uuid = str_replace("-", "", Str::uuid());
	  //$model->uuid = strtoupper(Str::random(5));  
	    $model->created_at = Carbon::now();
            $model->updated_at = Carbon::now();
            //  $model->status = 1;

            $baseClass = class_basename($model);
        });
        static::updating(function ($model) {
            $model->updated_at = Carbon::now();
        });
    }
}
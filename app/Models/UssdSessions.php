<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  Illuminate\Support\Str;
use Carbon\Carbon;
/*
 * +------------+-------------+------+-----+---------------------+-------------------------------+
| Field      | Type        | Null | Key | Default             | Extra                         |
+------------+-------------+------+-----+---------------------+-------------------------------+
| id         | int(11)     | NO   | PRI | NULL                | auto_increment                |
| uuid       | varchar(20) | NO   |     | NULL                |                               |
| msisdn     | bigint(20)  | NO   |     | NULL                |                               |
| session_id | bigint(20)  | NO   |     | NULL                |                               |
| state      | varchar(20) | NO   |     | NULL                |                               |
| created_at | timestamp   | NO   |     | current_timestamp() | on update current_timestamp() |
| updated_at | timestamp   | NO   |     | 0000-00-00 00:00:00 | on update current_timestamp() |
+------------+-------------+------+-----+---------------------+-------------------------------+

 */
class UssdSessions extends BaseModel
{
    use HasFactory;
    use HasFactory;
    protected $table  = 'ussd_sessions';
    protected $primaryKey = 'id';
    protected $fillable = [
'uuid' ,
'msisdn',
'session_id',
'state',
"hops_history",
"menu_function"
    ];
    protected $casts = [
        'hops_history' => 'array',
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
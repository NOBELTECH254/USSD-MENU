<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use  Illuminate\Support\Str;


class BaseModel extends Model
{
    use HasFactory;
    //  use AuditableTrait;
    use SoftDeletes;
    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var bool
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'clientID' => 'integer',
    ];
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
    public function editor()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (Auth::user()) {
                $user = Auth::user();
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
                $baseClass = class_basename($model);
            }


            //on reseller we shall ignore base model

            $model->uuid = str_replace("-", "", Str::uuid());
            $model->created_at = Carbon::now();
            $model->updated_at = Carbon::now();
            //  $model->status = 1;
            //if model has column then add it and model is not clients table

            $baseClass = class_basename($model);
        });
        static::updating(function ($model) {
            $updated_by = Auth::user()->id ?? 0;
            $model->updated_by = $updated_by;
            $model->updated_at = Carbon::now();
        });



        static::deleting(function ($model) {
            $user = Auth::user();
            $model->status = 5;
            $model->updated_by = $user->id;
            $model->updated_at = Carbon::now();
            $model->save();
            return true;
        });

        static::deleted(function ($model) {
            $user = Auth::user();
            $model->status = 5;
            $model->updated_by = $user->id;
            $model->updated_at = Carbon::now();
            $model->save();
            return true;
        });
    }
}

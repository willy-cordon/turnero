<?php

namespace App\Models;

use App\Models\Appointment;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TransportationVoucher extends Model
{
    protected $fillable = ['address'];

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $user_id = auth()->user() ? auth()->user()->id : 0;
            $model->created_by = $user_id;
            $model->updated_by = $user_id;
        });

        static::updating(function($model)
        {
            $user_id = auth()->user() ? auth()->user()->id : 0;
            $model->updated_by = $user_id;
        });
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getCreatedAtAttribute($value){
        if(!empty($value)){
            return Carbon::parse($value)->format(config('app.datetime_format'));
        }
        return null;
    }
}

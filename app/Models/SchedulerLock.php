<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SchedulerLock extends Model
{
    protected $fillable = ['lock_date','available_appointments'];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function getLockDateAttribute($value){
        if(!empty($value)){
            return Carbon::parse($value)->format(config('app.date_format'));
        }
        return null;
    }
    public function getLockDateOrder(){

         return Carbon::parse($this->getOriginal('lock_date'))->format('Ymd');

    }

    public function setLockDateAttribute($value){
        $this->attributes['lock_date'] = Carbon::createFromFormat(config('app.date_format'), $value);
    }
}

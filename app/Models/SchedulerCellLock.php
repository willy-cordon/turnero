<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchedulerCellLock extends Model
{
    use SoftDeletes;
    protected $fillable = ['lock_type', 'lock_date', 'lock_key', 'hour', 'dock_name'];

    const TYPE_LOCKS_CELL = 'Celda';
    const TYPE_LOCKS_DOCK = 'Circuito';
    const TYPE_LOCKS_HOUR = 'Hora';

    const TYPES = [self::TYPE_LOCKS_CELL,self::TYPE_LOCKS_DOCK,self::TYPE_LOCKS_HOUR];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }


    public function getCellLockDateAttribute($value){
        if(!empty($value)){
            return Carbon::parse($value)->format(config('app.date_format'));
        }
        return null;
    }



    public function setCellLockDateAttribute($value){
        $this->attributes['lock_date'] = Carbon::createFromFormat(config('app.date_format'), $value);
    }

}

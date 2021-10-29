<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;


/**
 * Class ActivityInstance
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class ActivityInstance extends Model
{
    protected $fillable = ['date', 'status', 'answer', 'created_by','fire_moment'];

    const ANSWER_YES = 'Si';
    const ANSWER_NO = 'No';
    const ANSWERS = [self::ANSWER_YES,self::ANSWER_NO];

    const STATUS_ALL = "Todas";
    const STATUS_TODO = "Pendiente";
    const STATUS_IN_PROGRESS = "En gestión";
    const STATUS_DONE = "Finalizada";
    const STATUS_CANCEL = "Cancelada";
    const STATUS_EXPIRED = "Expirada";
    const TRIGGER_ALL = "ALL";
    const TRIGGER_ALL_CREATE = "ALL_CREATE";
    const STATUS = [self::STATUS_TODO, self::STATUS_IN_PROGRESS, self::STATUS_DONE,self::STATUS_CANCEL];
    const STATUSTWO = [self::STATUS_ALL,self::STATUS_TODO, self::STATUS_IN_PROGRESS, self::STATUS_DONE,self::STATUS_CANCEL];

    const GROUP_TYPE_EDIARY     = 'E-Diary';
    const GROUP_TYPE_TURNOS     = 'Turnos';
    const GROUP_TYPE_VIGILANCIA = 'Vigilancia';


    public static function boot()
    {
        parent::boot();
        static::updating(function($model)
        {
            $user_id = auth()->user() ? auth()->user()->id : 0;
            $model->updated_by = $user_id;

        });

    }
    public function activity(){
        return $this->belongsTo(Activity::class)->withTrashed();
    }

    public function activityAction(){
        return $this->belongsTo(ActivityAction::class)->withTrashed();
    }

    public function appointment(){
        return $this->belongsTo(Appointment::class)->withTrashed();
    }

    public function users(){
        return $this->belongsTo(User::class);
    }

}

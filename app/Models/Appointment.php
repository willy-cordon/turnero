<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


/**
 * Class Appointment
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Appointment extends Model
{
    use SoftDeletes;
    protected $fillable = ['trucks_qty','sku_qty','packages_qty','pallets_qty','comments', 'transportation', 'required_date','start_date','end_date', 'is_reservation', 'need_assistance','next_step', 'created_by', 'original_created_by','date_range_status'];

    const STATUS_CONFIRM = 1;
    const STATUS_ACCOMPLISH = 2;
    const STATUS_DELAYED = 3; //En mo es re screening
    const STATUS_CANCELED = 4;
    const STATUS_IN_PLACE = 5;
    const NO_SHOW = 8;

    const IN_RANGE = 'EN RANGO';
    const OUT_RANGE = 'FUERA DE RANGO';

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $user_id = auth()->user() ? auth()->user()->id : 0;
            $model->created_by = $user_id;
            $model->original_created_by = $user_id;
            $model->updated_by = $user_id;

        });
        static::updating(function($model)
        {
            $user_id = auth()->user() ? auth()->user()->id : 0;
            $model->updated_by = $user_id;

        });

    }

    public function type()
    {
        return $this->belongsTo(AppointmentType::class)->withTrashed();
    }

    public function origin()
    {
        return $this->belongsTo(AppointmentOrigin::class)->withTrashed();
    }

    public function unloadType()
    {
        return $this->belongsTo(AppointmentUnloadType::class)->withTrashed();
    }

    public function action()
    {
        return $this->belongsTo(AppointmentAction::class)->withTrashed();
    }

    public function dock()
    {
        return $this->belongsTo(Dock::class)->withTrashed();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function activityInstances()
    {
        return $this->hasMany(ActivityInstance::class);
    }


    public function purchaseOrders(){
        return $this->belongsToMany(PurchaseOrder::class);
    }

    public function getCreatedAtAttribute($value){
        if(!empty($value)){
            return Carbon::parse($value)->format(config('app.datetime_format'));
        }
        return null;
    }

    public function getUpdatedAtAttribute($value){
        if(!empty($value)){
            return Carbon::parse($value)->format(config('app.datetime_format'));
        }
        return null;
    }

    public function getStartDateAttribute($value){
        if(!empty($value)){
            return Carbon::parse($value)->format(config('app.datetime_format'));
        }
        return null;
    }

    public function setStartDateAttribute($value){
        $this->attributes['start_date'] = Carbon::createFromFormat(config('app.datetime_format'), $value);
    }

    public function getEndDateAttribute($value){
        if(!empty($value)){
            return Carbon::parse($value)->format(config('app.datetime_format'));
        }
        return null;
    }

    public function setEndDateAttribute($value){
        $this->attributes['end_date'] = Carbon::createFromFormat(config('app.datetime_format'), $value);
    }

    public function getRequiredDateAttribute($value){
        if(!empty($value)){
            return Carbon::parse($value)->format(config('app.datetime_format'));
        }
        return null;
    }

    public function setRequiredDateAttribute($value){
        if(!empty($value)) {
            $this->attributes['required_date'] = Carbon::createFromFormat(config('app.datetime_format'), $value);
        }else{
            $this->attributes['required_date'] = $value;
        }
    }

    /*TODO: Move to a better place*/
    public static function changeToNoShow(){
        if(config('app.run_no_show_process') === true) {

            $noShowStatusId = config('app.no_show_status_id');
            if(!empty($noShowStatusId)) {
                $noShowAction = AppointmentAction::find($noShowStatusId);
                //CONFIRMED TO NO SHOW
                $appointmentsConfirmedQuery = Appointment::query();
                $appointmentsConfirmedQuery->where("action_id", "=", Appointment::STATUS_CONFIRM);
                $appointmentsConfirmedQuery->where("start_date","<", Carbon::now());
                $confirmedAppointments = $appointmentsConfirmedQuery->get();
                $confirmedAppointments->each(function ($confirmedAppointment) use ($noShowAction){
                    //echo "FROM CONFIRMED->".$confirmedAppointment->id . "," . $confirmedAppointment->getOriginal('start_date'). " - PASA A - NO SHOW"."new_line";

                    Log::debug("FROM CONFIRMED->".$confirmedAppointment->id . "," . $confirmedAppointment->getOriginal('start_date'). " - PASA A - NO SHOW");
                    $confirmedAppointment->action()->associate($noShowAction)->save();
                });

                //CANCEL TO NO SHOW

                $appointmentsQuery = Appointment::query();
                $appointmentsQuery->where("action_id", "=", Appointment::STATUS_CANCELED);
                $canceledAppointments = $appointmentsQuery->get();
                $canceledAppointments->each(function ($canceledAppointment) use($noShowAction) {
                    $appointmentDate = Carbon::parse($canceledAppointment->getOriginal('start_date'));
                    $appointmentUpdate = Carbon::parse($canceledAppointment->getOriginal('updated_at'));
                    $limitHour = Carbon::parse($canceledAppointment->getOriginal('updated_at'));
                    $limitHour->setHour(20)->setMinute(0)->setSecond(0);
                    $diff = $appointmentDate->diffInHours($appointmentUpdate);
                    if ($diff <= 24) {

                        $isSameDate = $appointmentDate->isSameDay($appointmentUpdate);
                        $status = "QUEDA EN - CANCELADO";
                        $changeStatus = false;
                        if (!$isSameDate) {
                            $limitDiff = $limitHour->diffInMinutes($appointmentUpdate, false);
                            if ($limitDiff >= 0) {
                                $status = "PASA A - NO SHOW";
                                $changeStatus = true;
                            }
                        } else {
                            $status = "PASA A - NO SHOW";
                            $changeStatus = true;
                        }
                       /* if($changeStatus) {
                            echo "FROM CANCELED -> " . $canceledAppointment->id .
                                "," . $canceledAppointment->getOriginal('start_date') .
                                "," . $canceledAppointment->getOriginal('updated_at') .
                                " ||||  limit datetime: " . $limitHour->format("Y-m-d H:i:s") .
                                " ||||  is same date: " . str_pad($isSameDate, 1, " ") .
                                "," . $status . "new_line";;
                        }*/

                        if($changeStatus){
                            Log::debug("FROM CANCELED -> ".$canceledAppointment->id .
                                "," . $canceledAppointment->getOriginal('start_date') .
                                "," . $canceledAppointment->getOriginal('updated_at') .
                                //" ||||  limit datetime: ".$limitHour->format("Y-m-d H:i:s").
                                //" ||||  is same date: ".str_pad($isSameDate, 1, " ").
                                "," . $status );
                            //" ||||  ".$diff."\n";
                            $canceledAppointment->action()->associate($noShowAction)->save();
                        }

                    }

                });
            }
        }
    }

}


<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Supplier
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Supplier extends Model
{
    use SoftDeletes;

    const GENDER_MALE   = 'Varón';
    const GENDER_FEMALE = 'Mujer';
    const GENDER_MALE2   = 'Varón Trans';
    const GENDER_FEMALE2 = 'Mujer Trans';
    const GENDER_OTHER = 'Otro';
    const GENDER_NO_ANSWER = 'Prefiere no decirlo';

    const GENDERS       = [self::GENDER_MALE,self::GENDER_FEMALE, self::GENDER_MALE2, self::GENDER_FEMALE2, self::GENDER_OTHER, self::GENDER_NO_ANSWER];

    const STATUS_ONE = 'SIGUE EN EL ESTUDIO';
    const STATUS_TWO = 'FUERA DEL ESTUDIO';
    const STATUS = [self::STATUS_ONE,self::STATUS_TWO];

    protected $fillable = ['wms_id','wms_name','client_id','address', 'email', 'phone', 'contact', 'aux1', 'aux2', 'aux3', 'aux4', 'aux5','wms_date', 'wms_gender','validate_address', 'validate_json', 'validate_longitude', 'validate_latitude', 'created_by', 'is_intervened','status','scheme_id','supplier_group_id','name','lastname','comorbidity'];
    protected $dates = ['wms_date'];
    protected $casts = ['validate_json'=>'json'];

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $user_id = auth()->user() ? auth()->user()->id : 0;
            $model->created_by = $user_id;
            $model->updated_by = $user_id;
            $model->recruiter_id = $user_id;
        });
        static::updating(function($model)
        {
            $user_id = auth()->user() ? auth()->user()->id : 0;
            $model->updated_by = $user_id;
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function supplierGroup()
    {
        return $this->belongsTo(SupplierGroup::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
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

    public function getWmsDateAttribute($value){
        if(!empty($value)){
            return Carbon::parse($value)->format(config('app.date_format'));
        }
        return null;
    }

    public function setWmsDateAttribute($value){
        if (!empty($value)){
            $this->attributes['wms_date'] = Carbon::createFromFormat(config('app.date_format'), $value);
        }
        return null;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords(strtolower($value));
    }

    public function setLastnameAttribute($value)
    {
        $this->attributes['lastname'] = ucwords(strtolower($value));
    }
}

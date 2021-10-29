<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierInterventionLog extends Model
{
    const REASON_ONE = '1. Vigilancia.';
    //const REASON_TWO = '2. Contacto estrecho.';
    const MIGRATION = '2. Migración.';
    const NO_INTERVENTION = '3. Desintervención.';


    const REASONS = [self::REASON_ONE];
    protected $fillable = ['intervention_reason', 'description', 'created_by'];
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

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

}

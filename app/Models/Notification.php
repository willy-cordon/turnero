<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    const APPOINTMENT = 'Turno';
    const ACTIVITY = 'Actividad';
    const NO_INTERVENTION = 'Desintervención';
    const INTERVENTION = 'Intervención';
    const MIGRATION = 'Migración';
    const STATUS = 'Estado';
    protected $fillable = ['appointment_id','activity_instance_id','email_subject','status','type','created_by'];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
}

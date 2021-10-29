<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentChangeLog extends Model
{
    protected $fillable = [ 'field_name', 'field_value_text', 'field_value_text_old', 'appointment_id', 'created_by'];

    public function appointment(){
        return $this->belongsTo(Appointment::class);
    }

}

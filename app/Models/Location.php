<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Location
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Location extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description', 'init_hour', 'end_hour', 'appointment_init_minutes_size', 'prev_action_id', 'prev_location_id', 'prev_days_from', 'prev_days_to', 'unique_appointment','appointment_created_bcc_emails','appointment_canceled_bcc_emails','enable_past_days','prev_location_id_workflow','sequence_id'];

    public function docks()
    {
        return $this->hasMany(Dock::class);
    }
    public function schemes()
    {
        return $this->belongsToMany(Scheme::class)->withTrashed();
    }
    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }
}

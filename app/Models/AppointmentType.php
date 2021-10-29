<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AppointmentType
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class AppointmentType extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}

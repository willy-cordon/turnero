<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AppointmentAction
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class AppointmentAction extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}

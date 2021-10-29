<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ActivityInstance
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */

class Activity extends Model
{
    const ANSWER_INIT = 'Creación de turno';
    const ANSWER_FINISH = 'Finalización de turno';
    const ANSWERS = [self::ANSWER_INIT,self::ANSWER_FINISH];


    use SoftDeletes;
    protected $fillable=['name','description','question_name','days_from_appointment','fire_moment'];
    public function activityGroup()
    {
        return $this->belongsTo(ActivityGroup::class)->withTrashed();
    }

    public function activityActions()
    {
        return $this->belongsToMany(ActivityAction::class)->withTrashed();
    }

    public function activityInstances()
    {
        return $this->hasMany(ActivityInstance::class);
    }


}

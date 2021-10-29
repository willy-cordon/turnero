<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityGroup extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','description','type','activity_group_type_id'];

    const ACTIVITY_GROUP_MANUAL = 'Manual';
    const ACTIVITY_GROUP_AUTOMATIC = 'Automática';

    const ACTIVITY_GROUP_TYPES = [self::ACTIVITY_GROUP_AUTOMATIC,self::ACTIVITY_GROUP_MANUAL];

    public function activities(){
        return $this->hasMany(Activity::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function activityGroupType()
    {
        return $this->belongsTo(ActivityGroupType::class);
    }

}

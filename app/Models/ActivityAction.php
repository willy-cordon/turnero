<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityAction extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','description','activity_status_triggered','activity_fired'];
    public function activityInstances()
    {
        return $this->hasMany(ActivityInstance::class);
    }


    public function activities()
    {
        return $this->belongsToMany(Activity::class)->withTrashed();
    }

    public function ActivityNotifications()
    {
        return $this->hasMany(ActivityNotification::class);
    }
}

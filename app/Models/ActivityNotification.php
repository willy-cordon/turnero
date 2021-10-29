<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityNotification extends Model
{
    protected $fillable = ['activity_action_id', 'subject', 'emails_to', 'emails_cc', 'trigger_status'];

    public function activityAction()
    {
        return $this->belongsTo(ActivityAction::class)->withTrashed();
    }
}

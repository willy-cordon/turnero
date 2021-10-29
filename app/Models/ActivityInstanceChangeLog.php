<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityInstanceChangeLog extends Model
{
    protected $fillable = [ 'value', 'value_old', 'activity_instance_id', 'created_by'];
}

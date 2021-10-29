<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sequence extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','description','show_in_workflow'];
}

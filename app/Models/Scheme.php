<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Scheme extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','description'];
    //

    public function locations()
    {
        return $this->belongsToMany(Location::class)->withTrashed();
    }
}

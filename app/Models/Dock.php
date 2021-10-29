<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Dock
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Dock extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description'];

    public function location()
    {
        return $this->belongsTo(Location::class)->withTrashed();
    }
}

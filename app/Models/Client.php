<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Client
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Client extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }
}

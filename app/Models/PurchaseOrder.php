<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchaseOrder
 * @package App\Models
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class PurchaseOrder extends Model
{
    protected $fillable = ['number', 'due_date', 'supplier_id', 'items', 'total_quantity'];

    public function getDueDateAttribute($value){
        return Carbon::parse($value)->format(config('app.date_format'));
    }

    public function setItemsAttribute($value){
        usort($value, $this->internal_key_sorter('line_number'));

        $this->attributes['items'] = json_encode($value);
    }
    public function getItemsAttribute($value){
        if($value == null)
           $value = json_encode([]);
        return  json_decode($value);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class);
    }

    //TODO: Move to helper?
    private function internal_key_sorter($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a->$key, $b->$key);
        };
    }
}

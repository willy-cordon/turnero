<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get($key, $default = null)
    {
        if ( self::has($key) ) {
            $setting = self::all()->where('key', $key)->first();
            return strlen($setting->value)>0 ? self::castValue($setting->value, $setting->type): $default;
        }
        return null;

    }

    public static function set($key, $value, $type = null){
        if($key){
            if ($type != null) {
                self::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);
            }else{
                self::updateOrCreate(['key' => $key], [ 'value' => $value]);
            }

        }
    }

    private static function has($key)
    {
        return (boolean) self::all()->whereStrict('key', $key)->count();
    }

    private static function castValue($val, $castTo)
    {
        switch ($castTo) {
            case 'int':
            case 'integer':
                return intval($val);
                break;
            case 'float':
                return floatval($val);
                break;
            case 'bool':
            case 'boolean':
                return boolval($val);
                break;

            default:
                return $val;
        }
    }


}

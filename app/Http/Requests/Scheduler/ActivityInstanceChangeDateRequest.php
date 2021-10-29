<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class ActivityInstanceChangeDateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'supplier_id'=> trans('scheduler.activity_instance_change_date.fields.supplier'),
            'activity_type_id'=> trans('scheduler.activity_instance_change_date.fields.activity_type'),
            'day'=> trans('scheduler.activity_instance_change_date.fields.day'),

        ];
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'supplier_id'=>'required',
            'activity_type_id'=>'required',
            'day'=>'required',
        ];
    }
}

<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class ActivityInstanceRequest extends FormRequest
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
            'supplier'=> trans('scheduler.activity_instances.fields.supplier'),
            'appointment'=> trans('scheduler.activity_instances.fields.appointment'),
            'activity'=> trans('scheduler.activity_instances.fields.activity'),
            'date'=> trans('scheduler.activity_instances.fields.date'),
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
            'supplier'=> 'required',
            'appointment'=>'required',
            'activity'=>'required',
            'date'=>'required',

        ];

    }
}

<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentUnloadTypeRequest extends FormRequest
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
     * Define nice names for attributes
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name'=>trans('scheduler.unload_types.fields.name')
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
            'name'=>'required|max:255'
        ];
    }
}

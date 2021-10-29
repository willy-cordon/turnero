<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class SupplierInterventionLogRequest extends FormRequest
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
            'reasons'=>trans('scheduler.supplier_intervention_log.fields.reasons_intervention'),
            'description'=>trans('scheduler.supplier_intervention_log.fields.description')
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
            'reasons'=> 'required',
//            'description' => 'required|max:255'
        ];
    }
}

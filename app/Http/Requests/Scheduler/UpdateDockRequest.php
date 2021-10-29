<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDockRequest extends FormRequest
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
            'name'=>trans('scheduler.docks.fields.name'),
            'location'=>trans('scheduler.docks.fields.location')
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
            'name'=>'required|max:255',
            'location'=>'required|exists:locations,id',
        ];
    }
}

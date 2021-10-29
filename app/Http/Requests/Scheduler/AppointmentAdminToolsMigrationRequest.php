<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentAdminToolsMigrationRequest extends FormRequest
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
            'supplier_id'=>trans('scheduler.supplier_migrations.fields.supplier_id'),
            'user_id'=>trans('scheduler.supplier_migrations.fields.user_id')
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
            'supplier_id'=>'required|exists:suppliers,id',
            'user_id'=>'required|exists:users,id'
        ];
    }
}

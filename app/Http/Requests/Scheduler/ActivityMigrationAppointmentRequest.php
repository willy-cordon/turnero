<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class ActivityMigrationAppointmentRequest extends FormRequest
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
            'appointment_id'=>trans('scheduler.activity_migrations.fields.appointment_id'),
            'users_migration'=>trans('scheduler.activity_migrations.fields.users_migration')
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
            'appointment_id'=>'required|numeric|exists:activity_instances,appointment_id',
            'users_migration'=>'required'
        ];
    }
}

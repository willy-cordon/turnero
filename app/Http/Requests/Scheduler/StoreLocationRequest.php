<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class StoreLocationRequest extends FormRequest
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
            'name'=>trans('scheduler.locations.fields.name'),
            'init_hour'=>trans('scheduler.settings.fields.init_hour'),
            'end_hour'=>trans('scheduler.settings.fields.end_hour'),
            'appointment_init_minutes_size'=>trans('scheduler.settings.fields.appointment_init_minutes_size'),
            'prev_days_to'=>trans('scheduler.locations.fields.prev_days_to'),
            'prev_days_from'=>trans('scheduler.locations.fields.prev_days_from'),
            'appointment_created_bcc_emails' =>trans('scheduler.locations.fields.bcc_emails_create'),
            'appointment_canceled_bcc_emails' =>trans('scheduler.locations.fields.bcc_emails_canceled'),
            'enable_past_days' =>trans('scheduler.locations.fields.enable_past_days'),
            'schemes' =>trans('scheduler.locations.fields.scheme'),

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
            'name'=>'required',
            'init_hour'=>'required',
            'end_hour'=>'required',
            'appointment_init_minutes_size'=>'required',
            'prev_days_to'=>'nullable|numeric',
            'prev_days_from'=>'nullable|numeric',
            'appointment_created_bcc_emails' => 'nullable|string',
            'appointment_canceled_bcc_emails' => 'nullable|string',
            'enable_past_days' => 'required',
            'schemes'=>'required',

        ];
    }
}

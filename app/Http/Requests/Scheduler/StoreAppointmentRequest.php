<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'supplier'=>trans('scheduler.appointments.fields.supplier'),
            'trucks_qty'=>trans('scheduler.appointments.fields.trucks_qty'),
            'sku_qty'=>trans('scheduler.appointments.fields.sku_qty'),
            'packages_qty'=>trans('scheduler.appointments.fields.packages_qty'),
            'pallets_qty'=>trans('scheduler.appointments.fields.pallets_qty'),
            'type'=>trans('scheduler.appointments.fields.type'),
            'action'=>trans('scheduler.appointments.fields.action'),
            'origin'=>trans('scheduler.appointments.fields.origin'),
            'unload_type'=>trans('scheduler.appointments.fields.unload_type'),
            'purchase_orders'=>trans('scheduler.appointments.fields.purchase_order'),
            'start_date'=>trans('scheduler.appointments.fields.date_hour'),
            'required_date'=>trans('scheduler.appointments.fields.required_date'),
            'transportation'=>trans('scheduler.appointments.fields.transportation'),
            'next_step'=>trans('scheduler.appointments.fields.next_step')
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validations = [
            'supplier'=>'required|exists:suppliers,id',
            'action'=>'required|exists:appointment_actions,id',
            'start_date'=>'required|max:255',
            'transportation'=>'required'
        ];
        $all_data = $this->all();

        if($all_data['action'] == 2){
            $validations['next_step']= 'required';
        }

        if($all_data['is_reservation'] == 0){
            $validations['purchase_orders']= 'required';
        }

        return $validations;
    }
}

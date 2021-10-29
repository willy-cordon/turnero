<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class SchedulerCellLockRequest extends FormRequest
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
            'lock_date'=>trans('scheduler.cell_locks.fields.lock_date'),
            'lock_type'=>trans('scheduler.cell_locks.fields.lock_type'),
            'hour'=>trans('scheduler.cell_locks.fields.hour'),
            'dock_name'=>trans('scheduler.cell_locks.fields.dock_name'),


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
            'lock_date'=>'required',
            'lock_type'=>'required',
        ];

        $all_data = $this->all();

        if($all_data['lock_type'] == 'Celda'){
            $validations['hour']= 'required';
            $validations['dock_name']= 'required';
        }
        if($all_data['lock_type'] == 'Circuito'){
            $validations['dock_name']= 'required';
        }
        if($all_data['lock_type'] == 'Hora'){
            $validations['hour']= 'required';
        }

        return $validations;
    }
}

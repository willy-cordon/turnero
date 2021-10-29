<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreSupplierRequest extends FormRequest
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
            'wms_name'=>trans('scheduler.suppliers.fields.wms_name'),
            'wms_id'=>trans('scheduler.suppliers.fields.wms_id'),
            'address'=>trans('scheduler.suppliers.fields.address'),
            'phone'=>trans('scheduler.suppliers.fields.phone'),
            'contact'=>trans('scheduler.suppliers.fields.contact'),
            'email'=>trans('scheduler.suppliers.fields.email'),
            'aux1'=>trans('scheduler.suppliers.fields.aux1'),
            'aux2'=>trans('scheduler.suppliers.fields.aux2'),
            'aux3'=>trans('scheduler.suppliers.fields.aux3'),
            'aux4'=>trans('scheduler.suppliers.fields.aux4'),
            'aux5'=>trans('scheduler.suppliers.fields.aux5'),
            'wms_date'=>trans('scheduler.suppliers.fields.wms_date'),
            'wms_gender'=>trans('scheduler.suppliers.fields.wms_gender'),
            'validate_address'=>trans('scheduler.suppliers.fields.validate_address'),
            'name'=>trans('scheduler.suppliers.fields.name'),
            'lastname'=>trans('scheduler.suppliers.fields.lastname'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validations =  [
//            'wms_name'=> 'required',
            'wms_id'=> 'required|numeric|unique:suppliers,wms_id',
            'address'=>'required',
            'phone'=>'required|numeric',
            'contact'=>'nullable|numeric',
            'email'=>'required|email:rfc',
            'aux1'=>'required',
            'aux2'=>'required|numeric',
            'aux4'=>'required',
            'wms_date'=>'required',
            'wms_gender'=>'required',
            'validate_address'=>'required'
        ];

        //Verificamos la variable de entorno SINGLE_NAME
        if (config('app.single_name')){
            $validations['wms_name']= 'required';
        }elseif(!config('app.single_name')){
            $validations['name']= 'required';
            $validations['lastname']= 'required';
        }

        return $validations;

    }
}

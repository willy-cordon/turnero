<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsersRequest extends FormRequest
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
            'name'=>trans('cruds.user.fields.name'),
            'email'=>trans('cruds.user.fields.email'),
            'dni'=>trans('cruds.user.fields.dni'),
            'phone'=>trans('cruds.user.fields.phone'),
            'password'=>trans('cruds.user.fields.password'),
            'roles'=>trans('cruds.user.fields.roles'),
            'supervisor_id' =>trans('cruds.user.fields.supervisor')
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'dni' => 'required|numeric',
            'phone' => 'required|numeric',
            'password' => 'required',
            'roles' => 'required'
        ];
    }
}

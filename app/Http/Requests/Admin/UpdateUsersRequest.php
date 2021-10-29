<?php
namespace App\Http\Requests\Admin;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUsersRequest extends FormRequest
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
            'roles'=>trans('cruds.user.fields.roles')
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
            'email' => 'required|email|unique:users,email,'.$this->route('user')->id.',id',
            'dni' => 'required|numeric',
            'phone' => 'required|numeric',
            'roles' => 'required',
        ];
    }
}

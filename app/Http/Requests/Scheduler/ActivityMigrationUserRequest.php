<?php

namespace App\Http\Requests\Scheduler;

use Illuminate\Foundation\Http\FormRequest;

class ActivityMigrationUserRequest extends FormRequest
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
            'userFrom'=>trans('scheduler.activity_migrations.fields.userFrom'),
            'userTo'=>trans('scheduler.activity_migrations.fields.userTo')
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
            'userFrom'=>'required|exists:activity_instances,created_by',
            'userTo'=>'required'
        ];
    }
}

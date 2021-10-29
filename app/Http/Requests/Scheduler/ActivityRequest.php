<?php

namespace App\Http\Requests\Scheduler;

use App\Models\ActivityGroup;
use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
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
            'name'=>trans('scheduler.activities.fields.name'),
            'question_name'=>trans('scheduler.activities.fields.question_name'),
            'days_from_appointment'=>trans('scheduler.activities.fields.days_from_appointment'),
            'actions'=>trans('scheduler.activities.fields.actions'),
            'email'=>trans('scheduler.activities.fields.email'),
            'group'=>trans('scheduler.activities.fields.group'),
            'fire_moment'=>trans('scheduler.activities.fields.created_activity'),
            'activity_group_id'=>trans('scheduler.activities.fields.group')
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
            'name'=> 'required',
            'question_name'=>'required|max:255',
            'activity_actions'=>'required',
            'activity_group_id'=>'required',

        ];
        $all_data = $this->all();

        if(empty($all_data['activity_group_id']) ||  ActivityGroup::find($all_data['activity_group_id'])->type == ActivityGroup::ACTIVITY_GROUP_AUTOMATIC){
            $validations['days_from_appointment'] = 'required|numeric';
            $validations['fire_moment'] = 'required';
        }

        return $validations;
    }
}

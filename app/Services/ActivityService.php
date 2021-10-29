<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{CreateModel, DestroyModel, ReadModel, UpdateModel};

final class ActivityService extends Service
{
    use CreateModel, ReadModel, UpdateModel, DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = Activity::class;
    }

    public function all(): Collection
    {
        return $this->model::withTrashed()->get();
    }

    public function restore($id)
    {
        return $this->model::withTrashed()->find($id)->restore();
    }



    public function create(Request $request): Model
    {
        $activity = $this->model::create($request->all());
        return  $this->save($activity, $request);

    }

    public function update(Model $activity, Request $request): Model
    {
        $activity->update($request->all());
        return $this->save($activity, $request);
    }

    public function save(Model $activity, Request $request)
    {

        $activity->activityGroup()->associate(ActivityGroup::find($request->activity_group_id))->save();
        $activity->activityActions()->sync($request->activity_actions);

        return $activity;

    }

}

<?php

namespace App\Services;

use App\Models\ActivityGroup;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{CreateModel, DestroyModel, ReadModel, UpdateModel};

final class LocationService extends Service
{
    use DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = Location::class;
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
        $location = $this->model::create($request->all());
        return  $this->save($location, $request);

    }

    public function update(Model $location, Request $request): Model
    {
        $location->update($request->all());
        return $this->save($location, $request);
    }

    public function save(Model $location, Request $request)
    {
        $location->schemes()->sync($request->schemes);
        return $location;
    }
}

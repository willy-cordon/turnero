<?php

namespace App\Services;

use App\Models\Dock;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{DestroyModel};

final class DockService extends Service
{
    use DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = Dock::class;
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
        $dock = $this->model::create($request->all());
        $dock->location()->associate(Location::find($request->location))->save();

        return $dock;
    }

    public function update( Model $dock, Request $request): Model
    {
        $dock->update($request->all());
        $dock->location()->associate(Location::find($request->location))->save();
        return $dock;
    }
}

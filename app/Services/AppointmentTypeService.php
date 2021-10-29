<?php

namespace App\Services;

use App\Models\AppointmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\CreateModel;
use Zendaemon\Services\Traits\DestroyModel;
use Zendaemon\Services\Traits\UpdateModel;

final class AppointmentTypeService extends Service
{
    use CreateModel;
    use UpdateModel;
    use DestroyModel;
    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = AppointmentType::class;
    }

    public function all(): Collection
    {
        return $this->model::withTrashed()->get();
    }

    public function restore($id)
    {
        return $this->model::withTrashed()->find($id)->restore();
    }

}

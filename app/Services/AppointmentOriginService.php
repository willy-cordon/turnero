<?php

namespace App\Services;

use App\Models\AppointmentOrigin;
use Illuminate\Support\Collection;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\CreateModel;
use Zendaemon\Services\Traits\DestroyModel;
use Zendaemon\Services\Traits\UpdateModel;

final class AppointmentOriginService extends Service
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
        $this->model = AppointmentOrigin::class;
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

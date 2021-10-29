<?php

namespace App\Services;

use App\Models\Sequence;
use Illuminate\Support\Collection;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{CreateModel, DestroyModel, ReadModel, UpdateModel};

final class SequenceService extends Service
{
    use CreateModel, ReadModel, UpdateModel, DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = Sequence::class;
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

<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use Illuminate\Support\Collection;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\CreateModel;
use Zendaemon\Services\Traits\DestroyModel;
use Zendaemon\Services\Traits\UpdateModel;

final class PurchaseOrderService extends Service
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
        $this->model = PurchaseOrder::class;
    }


    /**
     * @return Collection
     */
    public function all():Collection
    {
        return $this->model::all();
    }
}

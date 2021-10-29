<?php

namespace App\Services;

use App\Models\ActivityGroupType;
use App\Models\ActivityInstance;
use App\Models\ActivityInstanceChangeDate;
use App\User;
use Zendaemon\Services\Service;
use Zendaemon\Services\Traits\{CreateModel, DestroyModel, ReadModel, UpdateModel};

final class ActivityInstanceChangeDateService extends Service
{
    use CreateModel, ReadModel, UpdateModel, DestroyModel;

    /**
     * Set model class name.
     *
     * @return void
     */
    protected function setModel(): void
    {
        $this->model = ActivityInstanceChangeDate::class;
    }

    public function getDateChangeActivity()
    {
        $supplierPending = ActivityInstance::query();
        $supplierPending ->leftJoin('appointments','appointments.id','=','activity_instances.appointment_id');
        $supplierPending ->leftJoin('suppliers', 'suppliers.id','=','appointments.supplier_id');
        $supplierPending ->where('activity_instances.status' ,'=', ActivityInstance::STATUS_TODO);

        if(auth()->user()->hasRole(User::ROLE_SCHEDULER) ) {
            $supplierPending->where(function ($query) {
                $query->where('suppliers.created_by', auth()->user()->id)
                    ->orWhere('suppliers.recruiter_id', auth()->user()->id);
            });
        }

        $supplierPending ->select(['suppliers.id','suppliers.wms_id','suppliers.wms_name']);
        $supplierPendings = $supplierPending->orderBy('suppliers.wms_name','Asc')->get()->unique();

        $type_activities = ActivityGroupType::all();

        $days = ['monday' => 'Lunes', 'Tuesday' => 'Martes', 'Wednesday' => 'Miércoles', 'Thursday' => 'Jueves','friday'=>'Viernes','saturday' => 'Sábado', 'sunday' => 'Domingo' ];

        return compact('supplierPendings','type_activities', 'days');
    }

}

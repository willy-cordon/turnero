<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Supplier;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivityActionSelected extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Supplier $supplier, $subject)
    {
        $this->supplier = $supplier;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $appointments = Appointment::where('supplier_id', $this->supplier->id)
                        ->leftJoin('docks', 'appointments.dock_id', '=', 'docks.id' )
                        ->leftJoin('locations', 'docks.location_id', '=', 'locations.id')
                        ->select('appointments.*', 'locations.name as location_name')
                        ->get();

        $user = User::find($appointments->first()->created_by);

        $data = [
            "appointments"=>$appointments,
            'supplier_name' => $this->supplier->wms_name ,
            'supplier_dni' => $this->supplier->wms_id ,
            'supplier_mobile_phone' => $this->supplier->contact ,
            'supplier_phone' => $this->supplier->phone ,
            'supplier_contact' => $this->supplier->aux1 ,
            'supplier_contact_phone' => $this->supplier->aux2 ,
            'supplier_address' => $this->supplier->address.' '. $this->supplier->aux5.', '.$this->supplier->aux4,
            'supplier_email' => $this->supplier->email ,
            "scheduler_email"=>$user->email,
            "scheduler_name"=>$user->name,
            "scheduler_phone"=>$user->phone
        ];

        return $this->view('emails.activity_action_selected')->subject($this->subject.' - Voluntario: '. $this->supplier->wms_name.' [DNI: '. $this->supplier->wms_id.']')->with(['data'=>$data]);
    }
}

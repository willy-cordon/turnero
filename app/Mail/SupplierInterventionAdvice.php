<?php

namespace App\Mail;

use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierInterventionAdvice extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Supplier $supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'supplier_name' => $this->supplier->wms_name ,
            'supplier_dni' => $this->supplier->wms_id ,
            'supplier_mobile_phone' => $this->supplier->contact ,
            'supplier_phone' => $this->supplier->phone ,
            'supplier_contact' => $this->supplier->aux1 ,
            'supplier_contact_phone' => $this->supplier->aux2 ,
            'supplier_address' => $this->supplier->address.' '. $this->supplier->aux5.', '.$this->supplier->aux4,
            'supplier_email' => $this->supplier->email
        ];

        $subject = 'Voluntario intervenido por equipo de Vigilancia';

        return $this->view('emails.supplier_intervention')->subject($subject)->with(['data'=>$data]);

    }
}

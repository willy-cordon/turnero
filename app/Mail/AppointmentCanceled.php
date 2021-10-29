<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentCanceled extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $start_date;
    private $supplier;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $start_date, $subject, $supplier)
    {
        $this->user = $user;
        $this->start_date = $start_date;
        $this->subject = $subject;
        $this->supplier= $supplier;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = Carbon::createFromFormat(config('app.datetime_format'), $this->start_date);

        $data = [
            "appointment_date"=>Carbon::parse($date)->format('d/m/Y'),
            "appointment_hour"=>Carbon::parse($date)->format('H:i'),
            "scheduler_email"=>$this->user->email,
            "scheduler_name"=>$this->user->name,
            "scheduler_phone"=>$this->user->phone,
            "supplier_wms_name"=>$this->supplier->wms_name,
            "supplier_wms_id"=>$this->supplier->wms_id
        ];

        return $this->view('emails.appointment_canceled')->subject($this->subject)->with(['data'=>$data]);
    }
}

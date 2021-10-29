<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppointmentCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $supplier;
    public $start_date;
    public $transportation;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $supplier, $start_date, $transportation,$subject)
    {
        $this->user = $user;
        $this->supplier = $supplier;
        $this->start_date = $start_date;
        $this->transportation = $transportation;
        $this->subject = $subject;
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
            "transportation"=>$this->transportation,
            "name"=>$this->supplier->wms_name,
            "email"=>$this->supplier->email,
            "address"=>$this->supplier->address.' '.$this->supplier->aux5.', '.$this->supplier->aux4,
            "scheduler_email"=>$this->user->email,
            "scheduler_name"=>$this->user->name,
            "scheduler_phone"=>$this->user->phone,
            "mobile_phone_1"=>config('app.mobile_phone_1'),
            "mobile_phone_2"=>config('app.mobile_phone_2'),
        ];

        return $this->view('emails.appointment_created')->subject($this->subject)->with(['data'=>$data]);
    }
}

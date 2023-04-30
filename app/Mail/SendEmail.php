<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    // protected $email;
    protected $comanySymbol;
    protected $companyName;
    protected $startDate;
    protected $endDate;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($comanySymbol, $companyName, $startDate, $endDate)
    {
        $this->comanySymbol = $comanySymbol;
        $this->companyName = $companyName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('contact@xm-global.com', 'xm-global')
            ->subject('Submitted company symbol :' . $this->comanySymbol . ' name : '  . $this->companyName)
            ->markdown('emails.send_email', [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ]);
    }
}

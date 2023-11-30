<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApprovedRequest extends Mailable
{
    use Queueable;
    use SerializesModels;

    private $names;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($names)
    {
        $this->subject('Solicitud Aprobada con éxito');

        $this->names = $names;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.approved-requests', ['names' => $this->names]);
    }
}

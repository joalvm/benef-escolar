<?php

namespace App\Mail;

use App\Models\Persons;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CredentialsMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $subject = 'Credenciales de Acceso - Proceso 2021';

    /**
     * @var Persons
     */
    private $persons;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Persons $persons)
    {
        $this->persons = $persons;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            'id' => $this->persons->id,
            'name' => $this->persons->names,
            'password' => $this->persons->users->getTempPassword(),
            'token' => $this->persons->users->verification_token,
        ];

        return $this->view('mails.credentials', $data);
    }
}

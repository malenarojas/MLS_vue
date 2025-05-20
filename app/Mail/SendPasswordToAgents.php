<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendPasswordToAgents extends Mailable
{
    use Queueable, SerializesModels;

    protected $user_id;
    protected $password;

    /**
     * Create a new instance.
     */
    public function __construct($user_id, $password)
    {
        $this->user_id = $user_id;
        $this->password = $password;
    }

    public function build()
    {
        $user = User::find($this->user_id);
        $password = $this->password;

        return $this->subject('Bienvenido al nuevo Sistema!')
                    ->view('emails.send_password_to_agents')
                    ->with([
                        'header' => 'Bienvenido al nuevo Sistema!',
                        'user' => $user,
                        'password' => $password,
                    ]);
    }

}

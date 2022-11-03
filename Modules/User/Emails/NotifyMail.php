<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $emailInfo;
    public $type;

    public function __construct($emailInfo, $type)
    {
        $this->emailInfo = $emailInfo;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('¡Tienes una nuevo registro!')->view('user::layouts.email.notify_email');
    }
}

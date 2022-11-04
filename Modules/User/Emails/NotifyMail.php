<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bodyEmail;
    public $head;
    public $linkOrderPDF;
    public $type;

    public function __construct($bodyEmail, $head, $linkOrderPDF,$type)
    {
        $this->bodyEmail = $bodyEmail;
        $this->head = $head;
        $this->linkOrderPDF = $linkOrderPDF;
        $this->type = $type;
    }

    public function build()
    {
        return $this->subject('¡Tienes una nuevo registro!')->view('user::layouts.email.notify_email');
    }
}

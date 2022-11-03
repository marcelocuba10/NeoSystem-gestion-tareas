<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Mail;
use Modules\User\Emails\NotifyMail;

class SendEmailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);
    }

    public function notifyEmail(Request $request)
    {
        /** Get default email notification */
        $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();

        $details = [
            'userName' => 'Marcelo Cuba',
            'type' => 'Visita Cliente',
            'title' => 'Mail from ItSolutionStuff.com',
            'body' => 'This is for testing email using smtp'
        ];

        //return view('user::layouts.email.notify_email',compact('details'));

        //Mail::to($emailDefault)->send(new NotifyMail($details));

        if (Mail::failures()) {
            return response()->Fail('Sorry! Please try again latter');
        } else {
            //return back()->with('message', 'Great! Successfully send in your mail');
            return response()->json('Great! Successfully send in your mail');
        }
    }
}

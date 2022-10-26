<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CronJobsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function cronjob()
    {
        $currentDate = Carbon::now()->format('Y-m-d');

        //dd($currentDate);

        $appointments = DB::table('appointments')
            ->where('appointments.date', '<', $currentDate)
            ->select(
                'appointments.id',
                'appointments.visit_id',
                'appointments.date',
                'appointments.hour',
                'appointments.action',
                'appointments.status',
                'appointments.observation',
            )
            ->get();

            dd($appointments);

        return json_encode('complete');
    }
}

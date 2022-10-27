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
        $currentHour = Carbon::now()->format('H:i');

        $late_appointments = DB::table('appointments')
            ->where('appointments.date', '<=', $currentDate)
            ->where('appointments.status', '=', 'Pendiente')
            ->where('appointments.hour', '<', $currentHour)
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

        if ($late_appointments) {
            foreach ($late_appointments as $value) {
                /** if contain visit_id, update status in customer visit */
                if ($value->visit_id) {
                    DB::table('customer_visits')
                        ->where('customer_visits.id', '=', $value->visit_id)
                        ->update([
                            'status' => 'No Procesado',
                        ]);

                    DB::table('appointments')
                        ->where('appointments.id', '=', $value->id)
                        ->update([
                            'status' => 'No Procesado',
                        ]);
                } else {
                    DB::table('appointments')
                        ->where('appointments.id', '=', $value->id)
                        ->update([
                            'status' => 'No Procesado',
                        ]);
                }
            }
        }

        return json_encode('complete');
    }
}

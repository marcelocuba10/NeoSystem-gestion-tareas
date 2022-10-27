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
        $currentDateHour = Carbon::now()->format('Y-m-d H:i');

        /** get all appointments with status Pending */
        $late_appointments = DB::table('appointments')
            ->where('appointments.status', '=', 'Pendiente')
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

        foreach ($late_appointments as $value) {
            /** concatenate date + hour */
            $valueDateHour = $value->date . ' ' . $value->hour;

            /** if pass condition, alter status for not processed*/
            if ($valueDateHour < $currentDateHour) {
                /** if appointment contain relation with customer visit, alter status in two tables */
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

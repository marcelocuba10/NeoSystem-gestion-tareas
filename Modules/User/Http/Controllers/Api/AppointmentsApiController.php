<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Appointment;
use Modules\User\Entities\CustomerParameters;
use Modules\User\Entities\Customers;
use Modules\User\Entities\CustomerVisit;

use Mail;
use Modules\User\Emails\NotifyMail;

class AppointmentsApiController extends Controller
{
    public function index($idRefCurrentUser)
    {
        $appointments = DB::table('appointments')
            ->leftjoin('customers', 'customers.id', '=', 'appointments.customer_id')
            ->where('appointments.idReference', '=', $idRefCurrentUser)
            ->select(
                'appointments.id',
                'appointments.visit_number',
                'appointments.visit_id',
                'appointments.date',
                'appointments.hour',
                'appointments.action',
                'appointments.status',
                'appointments.observation',
                'customers.name AS customer_name',
                'customers.phone AS customer_phone',
            )
            ->orderBy('appointments.created_at', 'DESC')
            ->get();

        $customers = DB::table('customers')
            ->where('idReference', '=', $idRefCurrentUser)
            ->where('customers.status', '=', 1)
            ->orderBy('created_at', 'DESC')
            ->get();

        $actions = [
            'Realizar Llamada',
            'Realizar Visita',
        ];

        return response()->json(array(
            'appointments' => $appointments,
            'customers' => $customers,
            'actions' => $actions,
        ));
    }

    public function store(Request $request)
    {
        /** date validation, not less than 1980 and not greater than the current year **/
        $initialDate = '1980-01-01';
        $currentDate = (date('Y') + 2) . '-01-01'; //current date + 2 year

        $request->validate([
            'customer_id' => 'required',
            'date' => 'required|date|after_or_equal:today|before:' . $currentDate,
            'hour' => 'required|max:5|min:5',
            'action' => 'required|max:30|min:5',
            'observation' => 'nullable|max:1000|min:3',
            'idReference' => 'required'
        ]);

        $input = $request->all();

        /** Create new visit customer */
        $field['visit_number'] = $this->generateUniqueCodeVisit();
        $field['customer_id'] = $input['customer_id'];
        $field['seller_id'] = $input['idReference'];
        $field['visit_date'] = Carbon::now();
        $field['next_visit_date'] = $input['date'];
        $field['next_visit_hour'] = $input['hour'];
        $field['objective'] = null;
        $field['result_of_the_visit'] = null;
        $field['action'] = $input['action'];
        $field['type'] = 'Sin Presupuesto';
        $field['status'] = 'Pendiente';
        $customer_visit = CustomerVisit::create($field);

        /** Add input extra values and CREATE new appointment */
        $input['idReference'] = $input['idReference'];
        $input['visit_number'] = $customer_visit->visit_number;
        $input['visit_id'] = $customer_visit->id;
        $input['status'] = 'Pendiente';
        $appointment = Appointment::create($input);

        /** Send email notification */
        $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
        $head = 'crear una agenda - #' . $appointment->visit_number;
        $type = 'Agenda';
        $linkOrderPDF = null;

        $appointment = DB::table('appointments')
            ->leftjoin('customers', 'customers.id', '=', 'appointments.customer_id')
            ->leftjoin('users', 'users.idReference', '=', 'appointments.idReference')
            ->where('appointments.id', $appointment->id)
            ->select(
                'appointments.id',
                'appointments.visit_number',
                'appointments.date',
                'appointments.hour',
                'appointments.status',
                'appointments.action',
                'appointments.observation',
                'appointments.created_at',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.phone',
                'users.name AS seller_name'
            )
            ->first();

        Mail::to($emailDefault)->send(new NotifyMail($appointment, $head, $linkOrderPDF, $type));

        //return response
        return response()->json(array(
            'success' => 'Appointment created successfully.',
            'data'   => $appointment
        ));
    }

    public function generateUniqueCodeVisit()
    {
        do {
            $visit_number = random_int(100000, 999999);
        } while (
            DB::table('customer_visits')->where("visit_number", "=", $visit_number)->first()
        );

        return $visit_number;
    }

    public function search(Request $request, $idRefCurrentUser)
    {
        $search = $request->input('search');

        if ($search == '') {
            $appointments = DB::table('appointments')
                ->leftjoin('customers', 'customers.id', '=', 'appointments.customer_id')
                ->where('appointments.idReference', '=', $idRefCurrentUser)
                ->select(
                    'appointments.id',
                    'appointments.visit_number',
                    'appointments.visit_id',
                    'appointments.date',
                    'appointments.hour',
                    'appointments.action',
                    'appointments.status',
                    'appointments.observation',
                    'customers.name AS customer_name',
                    'customers.phone AS customer_phone',
                )
                ->orderBy('appointments.created_at', 'DESC')
                ->paginate(20);
        } else {
            $appointments = DB::table('appointments')
                ->leftjoin('customers', 'customers.id', '=', 'appointments.customer_id')
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->orWhere('appointments.visit_number', 'LIKE', "%{$search}%")
                ->where('appointments.idReference', '=', $idRefCurrentUser)
                ->select(
                    'appointments.id',
                    'appointments.visit_number',
                    'appointments.visit_id',
                    'appointments.date',
                    'appointments.hour',
                    'appointments.action',
                    'appointments.status',
                    'appointments.observation',
                    'customers.name AS customer_name',
                    'customers.phone AS customer_phone',
                )
                ->orderBy('appointments.created_at', 'DESC')
                ->paginate();
        }

        return view('user::appointments.index', compact('appointments', 'search'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function destroy($id)
    {
        //
    }
}

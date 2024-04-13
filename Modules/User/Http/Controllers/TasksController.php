<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Appointment;
use Modules\User\Entities\CustomerVisit;

use Mail;
use Modules\User\Emails\NotifyMail;
use Modules\User\Entities\Tasks;

class TasksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);
    }

    public function index()
    {
        $currentUserId = Auth::user()->id;

        $tasks = DB::table('tasks')
            ->leftjoin('users as a', 'a.id', '=', 'tasks.user_id')
            ->leftjoin('users as b', 'b.id', '=', 'tasks.assigned_to')
            ->where('a.id', '=', $currentUserId)
            ->select(
                'tasks.id',
                'tasks.user_id',
                'tasks.title',
                'tasks.description',
                'tasks.assigned_to',
                'tasks.priority',
                'tasks.status',
                'tasks.created_at',
                'b.name as user_assigned_name',
            )
            ->orderBy('tasks.created_at', 'DESC')
            ->paginate(20);

        return view('user::tasks.index', compact('tasks'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function create()
    {
        $currentUserId = Auth::user()->id;
        $task = null;

        $users = DB::table('users')
            ->where('users.status', '=', 1)
            ->select('users.id', 'users.name')
            ->get();

        $status = array(
            array('2', 'Terminado'),
            array('1', 'Pendiente'),
            array('0', 'Cancelado'),
        );

        $priority = array(
            array('2', 'Baja'),
            array('1', 'Media'),
            array('0', 'Alta'),
        );

        return view('user::tasks.create', compact('task', 'users', 'status', 'priority'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'assigned_to' => 'required',
                'title' => 'required|max:100|min:5',
                'status' => 'required',
                'priority' => 'required',
                'description' => 'nullable|max:1000|min:3',
            ],
            [
                'title.required'  => 'El campo Título es obligatorio.',
                'title.min'  => 'El campo Título debe ser mayor a 1 dígito.',
            ]
        );

        $input = $request->all();

        // Extra inputs
        $input['user_id'] = Auth::user()->id;

        Tasks::create($input);

        /** Send email notification */
        // $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();
        // $head = 'crear una agenda - #' . $appointment->visit_number;
        // $type = 'Agenda';
        // $linkOrderPDF = null;

        // $appointment = DB::table('appointments')
        //     ->leftjoin('customers', 'customers.id', '=', 'appointments.customer_id')
        //     ->leftjoin('users', 'users.idReference', '=', 'appointments.idReference')
        //     ->where('appointments.id', $appointment->id)
        //     ->select(
        //         'appointments.id',
        //         'appointments.visit_number',
        //         'appointments.date',
        //         'appointments.hour',
        //         'appointments.status',
        //         'appointments.action',
        //         'appointments.observation',
        //         'appointments.created_at',
        //         'customers.name AS customer_name',
        //         'customers.estate',
        //         'customers.phone',
        //         'users.name AS seller_name'
        //     )
        //     ->first();

        // Mail::to($emailDefault)->send(new NotifyMail($appointment, $head, $linkOrderPDF, $type));

        return redirect()->to('/user/tasks')->with('message', 'Tarea creada correctamente');
    }

    public function edit($id)
    {
        $idRefCurrentUser = Auth::user()->idReference;

        $task = Tasks::find($id);

        $users = DB::table('users')
            ->where('users.status', '=', 1)
            ->select('users.id', 'users.name')
            ->get();

        $status = array(
            array('2', 'Terminado'),
            array('1', 'Pendiente'),
            array('0', 'Cancelado'),
        );

        $priority = array(
            array('2', 'Baja'),
            array('1', 'Media'),
            array('0', 'Alta'),
        );

        return view('user::tasks.edit', compact('task', 'users', 'status', 'priority'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'assigned_to' => 'required',
                'title' => 'required|max:100|min:5',
                'status' => 'required',
                'priority' => 'required',
                'description' => 'nullable|max:1000|min:3',
            ],
            [
                'title.required'  => 'El campo Título es obligatorio.',
                'title.min'  => 'El campo Título debe ser mayor a 1 dígito.',
            ]
        );

        $input = $request->all();

        $task = Tasks::find($id);
        $task->update($input);

        return redirect()->to('/user/tasks')->with('message', 'Tarea actualizada correctamente.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $currentUserId = Auth::user()->id;

        if ($search == '') {
            $tasks = DB::table('tasks')
                ->leftjoin('users as a', 'a.id', '=', 'tasks.user_id')
                ->leftjoin('users as b', 'b.id', '=', 'tasks.assigned_to')
                ->where('a.id', '=', $currentUserId)
                ->select(
                    'tasks.id',
                    'tasks.user_id',
                    'tasks.title',
                    'tasks.description',
                    'tasks.assigned_to',
                    'tasks.priority',
                    'tasks.status',
                    'tasks.created_at',
                    'b.name as user_assigned_name',
                )
                ->orderBy('tasks.created_at', 'DESC')
                ->paginate(20);
        } else {
            $tasks = DB::table('tasks')
                ->leftjoin('users as a', 'a.id', '=', 'tasks.user_id')
                ->leftjoin('users as b', 'b.id', '=', 'tasks.assigned_to')
                ->where('a.id', '=', $currentUserId)
                ->where('tasks.title', 'LIKE', "%{$search}%")
                ->select(
                    'tasks.id',
                    'tasks.user_id',
                    'tasks.title',
                    'tasks.description',
                    'tasks.assigned_to',
                    'tasks.priority',
                    'tasks.status',
                    'tasks.created_at',
                    'b.name as user_assigned_name',
                )
                ->orderBy('tasks.created_at', 'DESC')
                ->paginate();
        }

        return view('user::tasks.index', compact('tasks', 'search'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function destroy($id)
    {
        Tasks::find($id)->delete();

        return redirect()->to('/user/tasks')->with('message', 'Registro eliminado correctamente');
    }
}

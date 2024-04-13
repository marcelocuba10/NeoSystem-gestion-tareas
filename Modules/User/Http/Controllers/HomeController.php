<?php

namespace Modules\User\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);
    }

    public function index()
    {
        $currentUserId = Auth::user()->id;
        $currentDate = Carbon::now()->format('d/m/Y');
        $currentOnlyYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        Carbon::setlocale('ES');
        $currentMonthName = Carbon::parse(Carbon::now()->format('Y/m/d'))->translatedFormat('F');

        $qty_tasks = DB::table('tasks')
            ->where('tasks.user_id', '=', $currentUserId)
            ->count();

        $qty_users = DB::table('users')
            ->where('users.status', '=', 1)
            ->count();

        $tasks = DB::table('tasks')
            ->where('tasks.user_id', '=', $currentUserId)
            ->get();

        $users = DB::table('users')
            ->where('users.status', '=', 1)
            ->select('id','name','email')
            ->get();

        return view('user::dashboard', compact(
            'tasks',
            'qty_tasks',
            'qty_users',
            'users',
            'currentMonthName',
            'currentDate',
            'currentOnlyYear',
        ));
    }
}

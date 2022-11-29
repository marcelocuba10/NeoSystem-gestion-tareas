<?php

namespace Modules\Admin\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\Parameters;

class ParametersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);

        $this->middleware('permission:parameter-sa-list|parameter-sa-create|parameter-sa-edit|parameter-sa-delete', ['only' => ['index']]);
        $this->middleware('permission:parameter-sa-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:parameter-sa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:parameter-sa-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $parameters = DB::table('parameters')
            ->where('type', '!=', 'Email')
            ->select(
                'id',
                'name',
                'type',
                'description',
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $emailDefault = DB::table('parameters')->where('type', 'Email')->pluck('email')->first();

        return view('admin::parameters.index', compact('parameters', 'emailDefault'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $parameter = null;
        $type_parameter = null;

        $keys = array(
            array('0', 'Rubro'),
            array('1', 'Equipos Potenciales')
        );

        return view('admin::parameters.create', compact('parameter', 'keys', 'type_parameter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|min:3',
            'type' => 'required|max:50|min:3',
            'description' => 'nullable|max:150|min:5',
        ]);

        $input = $request->all();
        Parameters::create($input);

        return redirect()->to('/admin/parameters')->with('message', 'Parameter created successfully.');
    }

    public function show($id)
    {
        $parameter = DB::table('parameters')
            ->where('id', '=', $id)
            ->select(
                'id',
                'name',
                'type',
                'description',
            )
            ->orderBy('created_at', 'DESC')
            ->first();

        return view('admin::parameters.show', compact('parameter'));
    }

    public function edit($id)
    {
        $parameter = DB::table('parameters')
            ->where('id', '=', $id)
            ->select(
                'id',
                'name',
                'type',
                'description',
            )
            ->orderBy('created_at', 'DESC')
            ->first();

        $type_parameter = $parameter->type;

        $keys = array(
            array('0', 'Rubro'),
            array('1', 'Equipos Potenciales')
        );

        return view('admin::parameters.edit', compact('parameter', 'keys', 'type_parameter'));
    }

    public function editEmailNotify()
    {
        $emailDefault = DB::table('parameters')->where('type', 'Email')->pluck('email')->first();

        return view('admin::parameters.email.edit', compact('emailDefault'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|min:3',
            'type' => 'required|max:50|min:3',
            'description' => 'nullable|max:150|min:5',
        ]);

        $input = $request->all();
        $parameter = Parameters::find($id);
        $parameter->update($input);

        return redirect()->to('/admin/parameters')->with('message', 'Parameter updated successfully.');
    }

    public function updateEmailNotify(Request $request)
    {
        $request->validate([
            'email' => 'required|max:50|min:5|email:rfc,dns',
        ]);

        DB::table('parameters')
            ->where('type', '=', 'Email')
            ->update([
                'email' => $request->email,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

        return redirect()->to('/admin/parameters')->with('message', 'Email actualizado correctamente.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $emailDefault = DB::table('parameters')->where('type', 'email')->pluck('email')->first();

        if ($search == '') {
            $parameters = DB::table('parameters')
                ->where('type', '!=', 'Email')
                ->select(
                    'id',
                    'name',
                    'type',
                    'description',
                )
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        } else {
            $parameters = DB::table('parameters')
                ->where('type', '!=', 'Email')
                ->where('name', 'LIKE', "%{$search}%")
                ->select(
                    'id',
                    'name',
                    'type',
                    'description',
                )
                ->orderBy('created_at', 'DESC')
                ->paginate();
        }

        return view('admin::parameters.index', compact('parameters', 'search', 'emailDefault'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function destroy($id)
    {
        Parameters::find($id)->delete();
        return redirect()->to('/admin/parameters')->with('message', 'Parameter deleted successfully');
    }
}

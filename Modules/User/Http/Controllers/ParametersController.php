<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Parameters;

class ParametersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:parameter-list|parameter-create|parameter-edit|parameter-delete', ['only' => ['index']]);
        $this->middleware('permission:parameter-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:parameter-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:parameter-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $parameters = DB::table('parameters')
            ->where('idReference', '=', $idRefCurrentUser)
            ->select(
                'id',
                'name',
                'type',
                'description',
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('user::parameters.index', compact('parameters'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $parameter = null;
        $type_parameter = null;

        $keys = array(
            array('0', 'Rubro'),
            array('1', 'Equipos Potenciales')
        );

        return view('user::parameters.create', compact('parameter', 'keys', 'type_parameter'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50|min:3',
            'type' => 'required|max:50|min:3',
            'description' => 'nullable|max:150|min:5',
        ]);

        $input = $request->all();

        /** relationship parameter with the current user IDreference */
        $input['idReference'] = Auth::user()->idReference;
        Parameters::create($input);

        return redirect()->to('/user/parameters')->with('message', 'Parameter created successfully.');
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

        return view('user::parameters.show', compact('parameter'));
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

        return view('user::parameters.edit', compact('parameter', 'keys', 'type_parameter'));
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

        return redirect()->to('/user/parameters')->with('message', 'Parameter updated successfully.');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $idRefCurrentUser = Auth::user()->idReference;

        if ($search == '') {
            $parameters = DB::table('parameters')
                ->where('idReference', '=', $idRefCurrentUser)
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
            ->where('idReference', '=', $idRefCurrentUser)
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

        return view('user::parameters.index', compact('parameters', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function destroy($id)
    {
        Parameters::find($id)->delete();
        return redirect()->to('/user/parameters')->with('message', 'Parameter deleted successfully');
    }
}

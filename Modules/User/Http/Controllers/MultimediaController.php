<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Modules\User\Entities\Multimedia;

class MultimediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:multimedia-list|multimedia-create|multimedia-edit|multimedia-delete', ['only' => ['index']]);
        $this->middleware('permission:multimedia-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:multimedia-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:multimedia-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $count_price_list = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Lista de Precios')
            ->count();

        $count_images = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Imágenes')
            ->count();

        $count_manuals = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Manuales')
            ->count();

        $count_docs = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Documentos')
            ->count();

        return view('user::multimedia.index', compact('count_price_list', 'count_images', 'count_manuals', 'count_docs'));
    }

    public function show($id)
    {
        $multimedia = Multimedia::find($id);

        return view('user::multimedia.show', compact('multimedia'));
    }

    public function findPrice(Request $request)
    {
        $product = DB::table('products')
            ->where('id', '=', $request->id)
            ->select('id', 'sale_price', 'inventory')
            ->first();

        if ($request->ajax()) {
            return response()->json($product);
        }
    }

    public function filter(Request $request)
    {
        $filter = $request->input('filter');

        if ($filter == '') {
            $multimedias = DB::table('multimedia')
                ->select(
                    'id',
                    'filename',
                    'description',
                    'type',
                    'size',
                    'created_at'
                )
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        } else {
            $multimedias = DB::table('multimedia')
                ->where('multimedia.type', 'LIKE', "%{$filter}%")
                ->select(
                    'id',
                    'filename',
                    'description',
                    'type',
                    'size',
                    'created_at'
                )
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        }

        return View::make('user::multimedia._partials.datatable', compact('multimedias', 'filter'))->with('i', (request()->input('page', 1) - 1) * 10);
    }
}

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
        $price_list = DB::table('multimedia')
            ->select(DB::raw('count(*) as count, sum(size) as sum'))
            ->where('multimedia.type', '=', 'Lista de Precios')
            ->first();

        $price_list_total_size = $this->formatBytes($price_list->sum);

        $images = DB::table('multimedia')
            ->select(DB::raw('count(*) as count, sum(size) as sum'))
            ->where('multimedia.type', '=', 'Imágenes')
            ->first();

        $images_total_size = $this->formatBytes($images->sum);

        $manuals = DB::table('multimedia')
            ->select(DB::raw('count(*) as count, sum(size) as sum'))
            ->where('multimedia.type', '=', 'Manuales')
            ->first();

        $manuals_total_size = $this->formatBytes($manuals->sum);

        $docs = DB::table('multimedia')
            ->select(DB::raw('count(*) as count, sum(size) as sum'))
            ->where('multimedia.type', '=', 'Documentos')
            ->first();

        $docs_total_size = $this->formatBytes($docs->sum);

        return view('user::multimedia.index', compact('price_list', 'price_list_total_size', 'images', 'images_total_size', 'manuals', 'manuals_total_size', 'docs', 'docs_total_size'));
    }

    public function show($id)
    {
        $multimedia = Multimedia::find($id);

        $file_size_format = $this->formatBytes($multimedia->size);
        $created_at_format = $multimedia->created_at->format('d/m/y H:i');

        return view('user::multimedia.show', compact('multimedia','file_size_format','created_at_format'));
    }

    function formatBytes($bytes, $precision = 2)
    {
        $units = ['Byte', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes > 1024) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $precision) . ' ' . $units[$i];
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

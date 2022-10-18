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

        $sum_price_list_size = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Lista de Precios')
            ->sum('size');

        $sum_price_list_size = $this->formatBytes($sum_price_list_size);

        $count_images = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Imágenes')
            ->count();

        $sum_images_size = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Imágenes')
            ->sum('size');

        $sum_images_size = $this->formatBytes($sum_images_size);

        $count_manuals = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Manuales')
            ->count();

        $sum_manuals_size = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Manuales')
            ->sum('size');

        $sum_manuals_size = $this->formatBytes($sum_manuals_size);

        $count_docs = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Documentos')
            ->count();

        $sum_docs_size = DB::table('multimedia')
            ->where('multimedia.type', '=', 'Documentos')
            ->sum('size');

        $sum_docs_size = $this->formatBytes($sum_docs_size);

        return view('user::multimedia.index', compact('count_price_list', 'count_images', 'count_manuals', 'count_docs', 'sum_price_list_size', 'sum_images_size', 'sum_manuals_size', 'sum_docs_size'));
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

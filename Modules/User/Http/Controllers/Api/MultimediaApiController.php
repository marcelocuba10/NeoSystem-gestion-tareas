<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class MultimediaApiController extends Controller
{
    public function index()
    {
        $multimedia = DB::table('multimedia')
            ->select(
                'id',
                'filename',
                'description',
                'type',
                'size',
                'created_at'
            )
            ->orderBy('created_at', 'DESC')
            ->get();

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

        return response()->json(array(
            'multimedia' => $multimedia,
            'count_price_list' => $count_price_list,
            'count_images' => $count_images,
            'count_manuals' => $count_manuals,
            'count_docs' => $count_docs,
        ));
    }

    public function filter($filter)
    {
        if ($filter == 'Imagenes') {
            $filter = 'Imágenes';
        }

        if ($filter == 'all') {
            $multimedia = DB::table('multimedia')
                ->select(
                    'id',
                    'filename',
                    'description',
                    'type',
                    'size',
                    'created_at'
                )
                ->orderBy('created_at', 'DESC')
                ->get();
        } else {
            $multimedia = DB::table('multimedia')
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
                ->get();
        }

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

        return response()->json(array(
            'multimedia' => $multimedia,
            'count_price_list' => $count_price_list,
            'count_images' => $count_images,
            'count_manuals' => $count_manuals,
            'count_docs' => $count_docs,
        ));
    }
}

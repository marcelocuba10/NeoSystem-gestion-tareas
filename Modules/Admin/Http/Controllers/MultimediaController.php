<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Modules\Admin\Entities\Multimedia;
use Illuminate\Support\Facades\File;

class MultimediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);

        $this->middleware('permission:multimedia-sa-list|multimedia-sa-create|multimedia-sa-edit|multimedia-sa-delete', ['only' => ['index']]);
        $this->middleware('permission:multimedia-sa-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:multimedia-sa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:multimedia-sa-delete', ['only' => ['destroy']]);
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

        return view('admin::multimedia.index', compact('price_list', 'price_list_total_size', 'images', 'images_total_size', 'manuals', 'manuals_total_size', 'docs', 'docs_total_size'));
    }

    public function show($id)
    {
        $multimedia = Multimedia::find($id);

        $file_size_format = $this->formatBytes($multimedia->size);
        $created_at_format = $multimedia->created_at->format('d/m/y H:i');

        return view('admin::multimedia.show', compact('multimedia', 'file_size_format', 'created_at_format'));
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'filename' => 'required|unique:images_products,filename',
            'filename' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf,xlsx,csv,xls|max:640000',
        ]);

        /** check if not select category */
        if (strlen($request->category) == 25) {
            return back()->with('error', 'Por favor, adicione una categoría al archivo antes de subir.');
        }

        $filesize = $request->file('filename')->getSize();

        $file = $request->file('filename');
        $filename = str_replace(' ', '-', $file->getClientOriginalName());
        $file->move(public_path('files/'), $filename);

        if ($request->fileId) {
            $statusFile = Multimedia::where('id', $request->fileId)
                ->update([
                    'filename' => str_replace('"', '', $filename),
                ]);

            $image_path = public_path("files/") . $request->oldFile;

            if (File::exists($image_path) && $request->oldFile != $filename) {
                File::delete($image_path);
            }
        } else {
            /** if the name already exists in the db */
            $multimedia = DB::table('multimedia')
                ->where('filename', '=', $filename)
                ->get();

            if (count($multimedia) > 0) {
                return back()->with('error', 'Ya existe un archivo con ese nombre.');
            } else {
                $statusFile = Multimedia::create([
                    'type' => $request->category,
                    'size' => $filesize,
                    'filename' => str_replace('"', '', $filename),
                ]);
            }
        }

        if (!is_null($statusFile)) {
            return back()->with("message", "Archivo cargado correctamente.");
        } else {
            return back()->with("error", "Failed to upload image.");
        }
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

    public function destroyFile($id)
    {
        $multimedia = Multimedia::find($id);
        Multimedia::where('id', $id)->delete();

        $file_path = public_path("files/") . $multimedia->filename;

        if (File::exists($file_path)) {
            File::delete($file_path);
        }

        return back()->with('success', 'Archivo eliminado correctamente');
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
                ->get();
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
                ->get();
        }

        return View::make('admin::multimedia._partials.datatable', compact('multimedias', 'filter'))->with('i', (request()->input('page', 1) - 1) * 10);
    }
}

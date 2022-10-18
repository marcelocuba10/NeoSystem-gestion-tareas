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

        return view('admin::multimedia.index', compact('count_price_list', 'count_images', 'count_manuals', 'count_docs', 'sum_price_list_size', 'sum_images_size', 'sum_manuals_size', 'sum_docs_size'));
    }

    public function show($id)
    {
        $multimedia = Multimedia::find($id);

        $file_size_format = $this->formatBytes($multimedia->size);
        $created_at_format = $multimedia->created_at->format('d/m/y H:i');

        return view('admin::multimedia.show', compact('multimedia','file_size_format','created_at_format'));
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'filename' => 'required|unique:images_products,filename',
            'filename.*' => 'mimes:jpeg,png,jpg,gif,svg,pdf,xlsx,csv,xls|max:50000',
        ]);

        /** check if not select category */
        if (strlen($request->category) == 25) {
            return back()->with('error', 'Por favor, adicione una categoría al archivo antes de subir.');
        } 

        $filesize = $request->file('filename')->getSize();

        $file = $request->file('filename');
        $filename = str_replace(' ', '-', $file->getClientOriginalName());
        $file->move(public_path('/images/files/'), $filename);

        if ($request->fileId) {
            $statusFile = Multimedia::where('id', $request->fileId)
                ->update([
                    'filename' => str_replace('"', '', $filename),
                ]);

            $image_path = public_path("/images/files/") . $request->oldFile;

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

        $file_path = public_path("/images/files/") . $multimedia->filename;

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

        return View::make('admin::multimedia._partials.datatable', compact('multimedias', 'filter'))->with('i', (request()->input('page', 1) - 1) * 10);
    }
}

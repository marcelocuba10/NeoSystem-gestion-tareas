<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Imports\ProductsImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportExportController extends Controller
{
    public function index()
    {
        return view('admin::products.import-products');
    }

    public function importcsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx'
        ]);

        $import = new ProductsImport;
        $file = $request->file('file');

        Excel::import($import, $file);

        /** when importing data, it does not respect the unique id, for which it is necessary to eliminate duplicate data by most oldest */
        $duplicateRecords = DB::table('products')
            ->select('custom_code', DB::raw('count(`custom_code`) as occurences'))
            ->groupBy('custom_code')
            ->having('occurences', '>', 1)
            ->get();

        foreach ($duplicateRecords as $duplicate) {
            DB::table('products')
                ->where('custom_code', $duplicate->custom_code)
                ->limit($duplicate->occurences - 1)
                ->orderBy('created_at', 'ASC')
                ->delete();
        }

        return redirect()->to('/admin/products')->with('message', 'El archivo ha sido importado correctamente');
    }
}

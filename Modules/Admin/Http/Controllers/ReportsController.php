<?php

namespace Modules\Admin\Http\Controllers;

use PDF;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);

        $this->middleware('permission:report-sa-list|report-sa-create|report-sa-edit|report-sa-delete', ['only' => ['index']]);
        $this->middleware('permission:report-sa-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:report-sa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:report-sa-delete', ['only' => ['destroy']]);
    }

    public function sellers(Request $request)
    {
        $sellers = DB::table('users')
            ->select(
                'id',
                'idReference',
                'name',
                'last_name',
                'doc_id',
                'email',
                'phone_1',
                'seller_contact_1',
                'address',
                'city',
                'estate',
                'status',
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        if ($request->has('download')) {
            $sellers = DB::table('users')
                ->select(
                    'id',
                    'idReference',
                    'name',
                    'last_name',
                    'doc_id',
                    'email',
                    'phone_1',
                    'seller_contact_1',
                    'address',
                    'city',
                    'estate',
                    'status',
                )
                ->orderBy('created_at', 'DESC')
                ->get();
                
            $pdf = PDF::loadView('admin::reports.sellersPrintPDF', compact('sellers'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }

        return view('admin::reports.sellers', compact('sellers'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function products(Request $request)
    {
        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'code',
                'purchase_price',
                'sale_price',
                'inventory',
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        if ($request->has('download')) {
            $products = DB::table('products')
                ->select(
                    'id',
                    'name',
                    'code',
                    'purchase_price',
                    'sale_price',
                    'inventory',
                )
                ->orderBy('code', 'DESC')
                ->get();

            $pdf = PDF::loadView('admin::reports.productsPrintPDF', compact('products'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }

        return view('admin::reports.products', compact('products'))->with('i', (request()->input('page', 1) - 1) * 30);
    }
}

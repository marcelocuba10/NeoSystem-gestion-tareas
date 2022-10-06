<?php

namespace Modules\User\Http\Controllers;

use PDF;
use Modules\User\Entities\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Customers;
use Modules\User\Entities\Reports;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:report-list|report-create|report-edit|report-delete', ['only' => ['index']]);
        $this->middleware('permission:report-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:report-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:report-delete', ['only' => ['destroy']]);
    }

    public function customers(Request $request)
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $customers = DB::table('customers')
            ->where('idReference', '=', $idRefCurrentUser)
            ->select(
                'id',
                'name',
                'doc_id',
                'email',
                'city',
                'estate',
                'phone',
                'is_vigia',
                'next_visit_hour',
                'next_visit_date'
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        if ($request->has('download')) {
            $customers = DB::table('customers')
                ->where('idReference', '=', $idRefCurrentUser)
                ->select(
                    'id',
                    'name',
                    'doc_id',
                    'email',
                    'city',
                    'estate',
                    'phone',
                    'is_vigia',
                    'next_visit_hour',
                    'next_visit_date'
                )
                ->orderBy('created_at', 'DESC')
                ->get();

            $pdf = PDF::loadView('user::reports.customersPrintPDF', compact('customers'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }

        return view('user::reports.customers', compact('customers'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function products(Request $request)
    {
        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'code',
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
                    'sale_price',
                    'inventory',
                )
                ->orderBy('code', 'DESC')
                ->get();

            $pdf = PDF::loadView('user::reports.productsPrintPDF', compact('products'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }

        return view('user::reports.products', compact('products'))->with('i', (request()->input('page', 1) - 1) * 30);
    }
}

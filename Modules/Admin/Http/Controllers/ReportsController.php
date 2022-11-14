<?php

namespace Modules\Admin\Http\Controllers;

use PDF;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

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
            ->get();

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

        $sales =null;

        return view('admin::reports.sellers', compact('sellers','sales'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function findSeller(Request $request)
    {
        $currentDate = Carbon::now()->format('d/m/Y');
        $currentOnlyYear = Carbon::now()->format('Y');
        $currentMonth = Carbon::now()->format('m');

        Carbon::setlocale('ES');
        $currentMonthName = Carbon::parse(Carbon::now()->format('Y/m/d'))->translatedFormat('F');

        $seller = DB::table('users')
            ->where('id', '=', $request->id)
            ->select(
                'id',
                'idReference',
                'name',
                'status',
                'phone_1',
                'doc_id',
                'estate'
            )
            ->first();

        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->leftjoin('users', 'users.idReference', '=', 'customer_visits.seller_id')
            ->where('customer_visits.seller_id', '=', $seller->idReference)
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.status',
                'customer_visits.action',
                'customer_visits.type',
                'customers.name AS customer_name',
                'customers.estate',
                'customers.phone',
                'users.name AS seller_name'
            )
            ->orderBy('customer_visits.created_at', 'DESC')
            ->limit(4)
            ->get();

        $sales = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->leftjoin('users', 'users.idReference', '=', 'sales.seller_id')
            ->where('customer_visits.seller_id', '=', $seller->idReference)
            ->select(
                'sales.id',
                'sales.customer_id',
                'sales.invoice_number',
                'sales.sale_date',
                'sales.type',
                'sales.status',
                'sales.total',
                'customers.name AS customer_name',
                'customers.estate',
                'customer_visits.visit_date',
                'users.name AS seller_name'
            )
            ->orderBy('sales.created_at', 'DESC')
            ->limit(5)
            ->get();

        $cant_customers = DB::table('customers')
            ->where('customers.idReference', '=', $seller->idReference)
            ->count();

        $visited_less_30_days = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->where('visit_date', '>', Carbon::now()->subDays(30))
            ->count();

        $visited_more_30_days = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->where('visit_date', '<', Carbon::now()->subDays(30))
            ->count();

        $visited_more_90_days = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->where('visit_date', '<', Carbon::now()->subDays(90))
            ->count();

        /** For Pie Chart */
        $visits_cancel_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'Cancelado')
            ->count();

        $visits_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'Procesado')
            ->count();

        $visits_no_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'No Procesado')
            ->count();

        $visits_pending_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->whereMonth('customer_visits.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('customer_visits.status', '=', 'Pendiente')
            ->count();

        $sales_count = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->whereMonth('sales.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('sales.previous_type', '=', 'Venta')
            ->where('sales.status', '=', 'Procesado')
            ->count();

        $orders_count = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->whereMonth('sales.created_at', $currentMonth) //get data current month 11,12 etc
            ->where('sales.previous_type', '=', 'Presupuesto')
            ->where('sales.status', '!=', 'Cancelado')
            ->count();

        /** For Column Chart */
        $getSalesCountByMonth = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->selectRaw("count(sales.id) as total, date_format(sales.created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('sales.created_at', '>=', $currentOnlyYear)
            ->where('sales.type', '=', 'Venta')
            ->where('sales.status', '=', 'Procesado')
            ->orderBy('sales.created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getSalesCancelCountByMonth = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->selectRaw("count(sales.id) as total, date_format(sales.created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('sales.created_at', '>=', $currentOnlyYear)
            ->where('sales.type', '=', 'Venta')
            ->where('sales.status', '=', 'Cancelado')
            ->orderBy('sales.created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getOrdersCountByMonth = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->selectRaw("count(sales.id) as total, date_format(sales.created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('sales.created_at', '>=', $currentOnlyYear)
            ->where('sales.previous_type', '=', 'Presupuesto')
            ->where('sales.status', '!=', 'Cancelado')
            ->orderBy('sales.created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getOrdersCancelCountByMonth = DB::table('sales')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $seller->idReference)
            ->selectRaw("count(sales.id) as total, date_format(sales.created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('sales.created_at', '>=', $currentOnlyYear)
            ->where('sales.type', '=', 'Presupuesto')
            ->where('sales.previous_type', '=', 'Presupuesto')
            ->where('sales.status', '=', 'Cancelado')
            ->orderBy('sales.created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $salesCountByMonth = $getSalesCountByMonth->pluck('total')->toArray();
        $salesCancelCountByMonth = $getSalesCancelCountByMonth->pluck('total')->toArray();
        $ordersCountByMonth = $getOrdersCountByMonth->pluck('total')->toArray();
        $ordersCancelCountByMonth = $getOrdersCancelCountByMonth->pluck('total')->toArray();

        //remove comillas ["1","3"]
        $salesCountByMonth = json_encode($salesCountByMonth);
        $salesCountByMonth = str_replace('"','',$salesCountByMonth);
        //decode json again [1,3]
        $salesCountByMonth = json_decode($salesCountByMonth);

        $salesCancelCountByMonth = json_encode($salesCancelCountByMonth);
        $salesCancelCountByMonth = str_replace('"','',$salesCancelCountByMonth);
        $salesCancelCountByMonth = json_decode($salesCancelCountByMonth);

        $ordersCountByMonth = json_encode($ordersCountByMonth);
        $ordersCountByMonth = str_replace('"','',$ordersCountByMonth);
        $ordersCountByMonth = json_decode($ordersCountByMonth);

        $ordersCancelCountByMonth = json_encode($ordersCancelCountByMonth);
        $ordersCancelCountByMonth = str_replace('"','',$ordersCancelCountByMonth);
        $ordersCancelCountByMonth = json_decode($ordersCancelCountByMonth);

        $salesPeriods = $getSalesCountByMonth->pluck('period')->toArray();

        //custom format date info
        $currentOnlyYear = json_encode($currentOnlyYear);
        $currentMonthName = json_encode($currentMonthName);

        if ($request->ajax()) {
            return response()->json(array(
                'customer_visits' => $customer_visits,
                'sales' => $sales,
                'cant_customers' => $cant_customers,
                'visited_less_30_days' => $visited_less_30_days,
                'visited_more_30_days' => $visited_more_30_days,
                'visited_more_90_days' => $visited_more_90_days,
                'visits_cancel_count' => $visits_cancel_count,
                'visits_process_count' => $visits_process_count,
                'visits_no_process_count' => $visits_no_process_count,
                'visits_pending_count' => $visits_pending_count,
                'seller' => $seller,
                'sales_count' => $sales_count,
                'orders_count' => $orders_count,
                'currentMonthName' => $currentMonthName,
                'currentOnlyYear' => $currentOnlyYear,
                'salesCountByMonth' => $salesCountByMonth,
                'ordersCountByMonth' => $ordersCountByMonth,
                'salesCancelCountByMonth' => $salesCancelCountByMonth,
                'ordersCancelCountByMonth' => $ordersCancelCountByMonth,
                'salesPeriods' => $salesPeriods
            ));
        }
    }

    public function products(Request $request)
    {
        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'code',
                'custom_code',
                'purchase_price',
                'sale_price',
                'updated_at',
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(30);

        if ($request->has('download')) {
            $products = DB::table('products')
                ->select(
                    'id',
                    'name',
                    'code',
                    'custom_code',
                    'purchase_price',
                    'sale_price',
                    'updated_at',
                )
                ->orderBy('code', 'DESC')
                ->get();

            $pdf = PDF::loadView('admin::reports.productsPrintPDF', compact('products'));
            return $pdf->stream();
            // return $pdf->download('pdfview.pdf');
        }

        return view('admin::reports.products', compact('products'))->with('i', (request()->input('page', 1) - 1) * 30);
    }

    public function graphs()
    {
        $idRefCurrentUser = Auth::user()->idReference;
        $currentDate = Carbon::now()->format('d/m/Y');
        $currentOnlyYear = Carbon::now()->format('Y');

        /** For Pie Chart */
        $visits_cancel_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.status', '=', 'Cancelado')
            ->count();

        $visits_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.status', '=', 'Procesado')
            ->count();

        $visits_no_process_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.status', '=', 'No Procesado')
            ->count();

        $visits_pending_count = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->where('customer_visits.status', '=', 'Pendiente')
            ->count();

        $sales_count = DB::table('sales')
            ->where('previous_type', '=', 'Venta')
            ->where('status', '=', 'Procesado')
            ->count();

        $orders_count = DB::table('sales')
            ->where('previous_type', '=', 'Presupuesto')
            ->where('status', '!=', 'Cancelado')
            ->count();

        /** For Column Chart */
        $getSalesCountByMonth = DB::table('sales')
            ->selectRaw("count(id) as total, date_format(created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('created_at', '>=', $currentOnlyYear)
            ->where('type', '=', 'Venta')
            ->where('status', '=', 'Procesado')
            ->orderBy('created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getSalesCancelCountByMonth = DB::table('sales')
            ->selectRaw("count(id) as total, date_format(created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('created_at', '>=', $currentOnlyYear)
            ->where('type', '=', 'Venta')
            ->where('status', '=', 'Cancelado')
            ->orderBy('created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getOrdersCountByMonth = DB::table('sales')
            ->selectRaw("count(id) as total, date_format(created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('created_at', '>=', $currentOnlyYear)
            ->where('previous_type', '=', 'Presupuesto')
            ->where('status', '!=', 'Cancelado')
            ->orderBy('created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $getOrdersCancelCountByMonth = DB::table('sales')
            ->selectRaw("count(id) as total, date_format(created_at, '%b %Y') as period")  //Essentially, what this selection date_format(created_at, '%b %Y') does is that it maps the created_at field into a string containing the field's month and year, like 'Mar 2022'.
            ->whereYear('created_at', '>=', $currentOnlyYear)
            ->where('previous_type', '=', 'Presupuesto')
            ->where('status', '=', 'Cancelado')
            ->orderBy('created_at', 'ASC')
            ->groupBy('period')
            ->get();

        $salesCountByMonth = $getSalesCountByMonth->pluck('total')->toArray();
        $salesCancelCountByMonth = $getSalesCancelCountByMonth->pluck('total')->toArray();
        $ordersCountByMonth = $getOrdersCountByMonth->pluck('total')->toArray();
        $ordersCancelCountByMonth = $getOrdersCancelCountByMonth->pluck('total')->toArray();

        $salesPeriods = $getSalesCountByMonth->pluck('period')->toArray();

        return view('admin::reports.graphs', compact(
            'sales_count',
            'orders_count',
            'salesCountByMonth',
            'salesCancelCountByMonth',
            'ordersCountByMonth',
            'ordersCancelCountByMonth',
            'salesPeriods',
            'visits_cancel_count',
            'visits_pending_count',
            'visits_process_count',
            'visits_no_process_count',
        ));
    }

    public function customer_visits()
    {
        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        $status = [
            'Procesado',
            'No Procesado',
            'Cancelado'
        ];

        $visits_labels = [
            'Menos de 30 días',
            'Más de 30 días',
            'Más de 90 días'
        ];

        $estates = [
            'Alto Paraná',
            'Central',
            'Concepción',
            'San Pedro',
            'Cordillera',
            'Guairá',
            'Caaguazú',
            'Caazapá',
            'Itapúa',
            'Misiones',
            'Paraguarí',
            'Ñeembucú',
            'Amambay',
            'Canindeyú',
            'Presidente Hayes',
            'Boquerón',
            'Alto Paraguay'
        ];

        $potential_products = DB::table('parameters')
            ->where('type', '=', 'Equipos Potenciales')
            ->get();

        return view('admin::reports.customer_visits', compact('categories', 'potential_products', 'estates', 'status', 'visits_labels'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function visit_on_map()
    {
        $customer_visits = DB::table('customer_visits')
            ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
            ->select(
                'customer_visits.id',
                'customer_visits.visit_number',
                'customer_visits.visit_date',
                'customer_visits.next_visit_date',
                'customer_visits.next_visit_hour',
                'customer_visits.status',
                'customer_visits.type',
                'customer_visits.action',
                'customer_visits.customer_id',
                'customers.name AS customer_name',
                'customers.city',
                'customers.latitude',
                'customers.longitude',
            )
            //->groupBy('customers.id')
            ->orderBy('customer_visits.visit_date', 'ASC')
            ->get();

        return view('admin::reports.visits_on_map', compact('customer_visits',));
    }

    public function filter_visits(Request $request)
    {
        $filter = $request->input('filter');
        $type = $request->input('type');

        if ($filter == '') {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->select(
                    'customer_visits.id',
                    'customer_visits.visit_number',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customers.category'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->get();
        } else {

            if ($type == 'estate') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customers.estate', 'LIKE', "%{$filter}%")
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_number',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'status') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customer_visits.status', 'LIKE', "{$filter}%")
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_number',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'category') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->leftjoin('customer_parameters', 'customer_parameters.customer_id', '=', 'customers.id')
                    ->where('customer_parameters.category_id', '=', $filter)
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_number',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'potential_products') {
                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->leftjoin('customer_parameters', 'customer_parameters.customer_id', '=', 'customers.id')
                    ->where('customer_parameters.potential_product_id', '=', $filter)
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_number',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'visit_date') {

                if ($filter == 'Menos de 30 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('visit_date', '>', Carbon::now()->subDays(30))
                        ->select(
                            'customer_visits.id',
                            'customer_visits.visit_number',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.status',
                            'customer_visits.type',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.category'
                        )
                        ->orderBy('customer_visits.created_at', 'DESC')
                        ->get();
                }

                if ($filter == 'Más de 30 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('visit_date', '<', Carbon::now()->subDays(30))
                        ->select(
                            'customer_visits.id',
                            'customer_visits.visit_number',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.status',
                            'customer_visits.type',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.category'
                        )
                        ->orderBy('customer_visits.created_at', 'DESC')
                        ->get();
                }

                if ($filter == 'Más de 90 días') {
                    $customer_visits = DB::table('customer_visits')
                        ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                        ->where('visit_date', '<', Carbon::now()->subDays(90))
                        ->select(
                            'customer_visits.id',
                            'customer_visits.visit_number',
                            'customer_visits.visit_date',
                            'customer_visits.next_visit_date',
                            'customer_visits.status',
                            'customer_visits.type',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customers.category'
                        )
                        ->orderBy('customer_visits.created_at', 'DESC')
                        ->get();
                }
            } elseif ($type == 'next_visit_date') {

                $customer_visits = DB::table('customer_visits')
                    ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                    ->where('customer_visits.next_visit_date', 'LIKE', "{$filter}%")
                    ->select(
                        'customer_visits.id',
                        'customer_visits.visit_number',
                        'customer_visits.visit_date',
                        'customer_visits.next_visit_date',
                        'customer_visits.status',
                        'customer_visits.type',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customers.category'
                    )
                    ->orderBy('customer_visits.created_at', 'DESC')
                    ->get();
            }
        }

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        return View::make('admin::reports._partials.datatable', compact('customer_visits', 'filter', 'categories'));
    }

    public function search_visits(Request $request)
    {
        $search = $request->input('search');

        if ($search == '') {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->select(
                    'customer_visits.id',
                    'customer_visits.visit_number',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customers.category'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->get();
        } else {
            $customer_visits = DB::table('customer_visits')
                ->leftjoin('customers', 'customers.id', '=', 'customer_visits.customer_id')
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->select(
                    'customer_visits.id',
                    'customer_visits.visit_number',
                    'customer_visits.visit_date',
                    'customer_visits.next_visit_date',
                    'customer_visits.next_visit_hour',
                    'customer_visits.result_of_the_visit',
                    'customer_visits.objective',
                    'customer_visits.status',
                    'customer_visits.type',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customers.category'
                )
                ->orderBy('customer_visits.created_at', 'DESC')
                ->get();
        }

        $customers_categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->pluck('name', 'name');

        $status = [
            'Procesado',
            'No Procesado',
            'Cancelado'
        ];

        $visits_labels = [
            'Menos de 30 días',
            'Más de 30 días',
            'Más de 90 días'
        ];

        $estates = [
            'Alto Paraná',
            'Central',
            'Concepción',
            'San Pedro',
            'Cordillera',
            'Guairá',
            'Caaguazú',
            'Caazapá',
            'Itapúa',
            'Misiones',
            'Paraguarí',
            'Ñeembucú',
            'Amambay',
            'Canindeyú',
            'Presidente Hayes',
            'Boquerón',
            'Alto Paraguay'
        ];

        $categories = DB::table('parameters')
            ->where('type', '=', 'Rubro')
            ->select('id', 'name')
            ->get();

        return View::make('admin::reports._partials.datatable', compact('customer_visits', 'search', 'categories'));
    }

    public function sales()
    {
        $idRefCurrentUser = Auth::user()->idReference;

        $sales = DB::table('sales')
            ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
            ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
            ->where('customers.idReference', '=', $idRefCurrentUser)
            ->select(
                'sales.id',
                'sales.customer_id',
                'sales.invoice_number',
                'sales.sale_date',
                'sales.type',
                'sales.status',
                'sales.total',
                'customers.name AS customer_name',
                'customers.estate',
                'customer_visits.visit_date',
            )
            ->orderBy('sales.created_at', 'DESC')
            ->get();

        $status = [
            'Pendiente',
            'Procesado',
            'Cancelado'
        ];

        $visits_labels = [
            'Menos de 30 días',
            'Más de 30 días',
            'Más de 90 días'
        ];

        $types = [
            'Presupuesto',
            'Venta',
        ];

        return view('admin::reports.sales', compact('sales', 'status', 'visits_labels', 'types'))->with('i', (request()->input('page', 1) - 1) * 20);
    }

    public function filter_sales(Request $request)
    {
        $filter = $request->input('filter');
        $type = $request->input('type');

        if ($filter == '') {
            $sales = DB::table('sales')
                ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->select(
                    'sales.id',
                    'sales.customer_id',
                    'sales.invoice_number',
                    'sales.sale_date',
                    'sales.type',
                    'sales.status',
                    'sales.total',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customer_visits.visit_date',
                )
                ->orderBy('sales.created_at', 'DESC')
                ->get();
        } else {
            if ($type == 'status') {
                $sales = DB::table('sales')
                    ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                    ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                    ->where('sales.status', 'LIKE', "{$filter}%")
                    ->select(
                        'sales.id',
                        'sales.invoice_number',
                        'sales.sale_date',
                        'sales.type',
                        'sales.status',
                        'sales.total',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customer_visits.visit_date',
                    )
                    ->orderBy('sales.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'types') {
                $sales = DB::table('sales')
                    ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                    ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                    ->where('sales.type', 'LIKE', "{$filter}%")
                    ->select(
                        'sales.id',
                        'sales.invoice_number',
                        'sales.sale_date',
                        'sales.type',
                        'sales.status',
                        'sales.total',
                        'customers.name AS customer_name',
                        'customers.estate',
                        'customer_visits.visit_date',
                    )
                    ->orderBy('sales.created_at', 'DESC')
                    ->get();
            } elseif ($type == 'visit_date') {
                if ($filter == 'Menos de 30 días') {

                    $sales = DB::table('sales')
                        ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                        ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                        ->where('sales.updated_at', '>', Carbon::now()->subDays(30))
                        ->select(
                            'sales.id',
                            'sales.invoice_number',
                            'sales.sale_date',
                            'sales.type',
                            'sales.status',
                            'sales.total',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customer_visits.visit_date',
                        )
                        ->orderBy('sales.created_at', 'DESC')
                        ->get();
                }

                if ($filter == 'Más de 30 días') {
                    $sales = DB::table('sales')
                        ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                        ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                        ->where('sales.updated_at', '<', Carbon::now()->subDays(30))
                        ->select(
                            'sales.id',
                            'sales.invoice_number',
                            'sales.sale_date',
                            'sales.type',
                            'sales.status',
                            'sales.total',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customer_visits.visit_date',
                        )
                        ->orderBy('sales.created_at', 'DESC')
                        ->get();
                }

                if ($filter == 'Más de 90 días') {
                    $sales = DB::table('sales')
                        ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                        ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                        ->where('sales.updated_at', '<', Carbon::now()->subDays(90))
                        ->select(
                            'sales.id',
                            'sales.invoice_number',
                            'sales.sale_date',
                            'sales.type',
                            'sales.status',
                            'sales.total',
                            'customers.name AS customer_name',
                            'customers.estate',
                            'customer_visits.visit_date',
                        )
                        ->orderBy('sales.created_at', 'DESC')
                        ->get();
                }
            }
        }

        return View::make('admin::reports._partials.datatable-sales', compact('sales', 'filter'));
    }

    public function search_sales(Request $request)
    {
        $search = $request->input('search');

        if ($search == '') {
            $sales = DB::table('sales')
                ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->select(
                    'sales.id',
                    'sales.customer_id',
                    'sales.invoice_number',
                    'sales.sale_date',
                    'sales.type',
                    'sales.status',
                    'sales.total',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customer_visits.visit_date',
                )
                ->orderBy('sales.created_at', 'DESC')
                ->get();
        } else {
            $sales = DB::table('sales')
                ->leftjoin('customer_visits', 'customer_visits.id', '=', 'sales.visit_id')
                ->leftjoin('customers', 'customers.id', '=', 'sales.customer_id')
                ->where('customers.name', 'LIKE', "%{$search}%")
                ->select(
                    'sales.id',
                    'sales.customer_id',
                    'sales.invoice_number',
                    'sales.sale_date',
                    'sales.type',
                    'sales.status',
                    'sales.total',
                    'customers.name AS customer_name',
                    'customers.estate',
                    'customer_visits.visit_date',
                )
                ->orderBy('sales.created_at', 'DESC')
                ->get();
        }

        return View::make('admin::reports._partials.datatable-sales', compact('sales', 'search'));
    }
}

<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\User\Entities\Products;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['logout']]);

        $this->middleware('permission:products-list|products-create|products-edit|products-delete', ['only' => ['index']]);
        $this->middleware('permission:products-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:products-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:products-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        // $products = DB::table('products')
        //     ->leftjoin('images_products', 'images_products.code_product', '=', 'products.code')
        //     ->select(
        //         'products.id',
        //         'products.name',
        //         'products.description',
        //         'products.sale_price',
        //         'products.inventory',
        //         'images_products.filename'
        //     )
        //     ->orderBy('products.created_at', 'DESC')
        //     ->groupBy('code')
        //     ->paginate(10);

        $products = DB::table('products')
            ->select(
                'products.id',
                'products.custom_code',
                'products.name',
                'products.description',
                'products.sale_price',
                'products.purchase_price'
            )
            ->orderBy('products.created_at', 'DESC')
            ->paginate(30);

        return view('user::products.index', compact('products'));
    }

    public function show($id)
    {
        $product = DB::table('products')
            ->where('products.id', '=', $id)
            ->select(
                'products.id',
                'products.custom_code',
                'products.name',
                'products.description',
                'products.sale_price',
                'products.purchase_price'
            )
            ->first();
            
        // $images = DB::table('images_products')
        //     ->where('code_product', '=', $product->code)
        //     ->get();

        return view('user::products.show', compact('product'));
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

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search == '') {
            // $products = DB::table('products')
            //     ->leftjoin('images_products', 'images_products.code_product', '=', 'products.code')
            //     ->select(
            //         'products.id',
            //         'products.name',
            //         'products.description',
            //         'products.sale_price',
            //         'products.inventory',
            //         'products.purchase_price',
            //         'images_products.filename'
            //     )
            //     ->orderBy('products.created_at', 'DESC')
            //     ->groupBy('code')
            //     ->paginate(10);

            $products = DB::table('products')
                ->select(
                    'products.id',
                    'products.custom_code',
                    'products.name',
                    'products.description',
                    'products.purchase_price',
                    'products.sale_price',
                )
                ->orderBy('products.created_at', 'DESC')
                ->paginate(30);
        } else {
            // $products = DB::table('products')
            //     ->where('products.name', 'LIKE', "%{$search}%")
            //     ->leftjoin('images_products', 'images_products.code_product', '=', 'products.code')
            //     ->select(
            //         'products.id',
            //         'products.name',
            //         'products.description',
            //         'products.sale_price',
            //         'products.inventory',
            //         'images_products.filename'
            //     )
            //     ->orderBy('products.created_at', 'DESC')
            //     ->groupBy('code')
            //     ->paginate();

            $products = DB::table('products')
                ->where('products.name', 'LIKE', "%{$search}%")
                ->orWhere('products.custom_code', 'LIKE', "%{$search}%")
                ->select(
                    'products.id',
                    'products.custom_code',
                    'products.name',
                    'products.description',
                    'products.purchase_price',
                    'products.sale_price',
                )
                ->orderBy('products.created_at', 'DESC')
                ->paginate(30);
        }

        return view('user::products.index', compact('products', 'search'));
    }
}

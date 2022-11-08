<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ProductsApiController extends Controller
{
    public function index()
    {
        $products = DB::table('products')
            ->select(
                'products.id',
                'products.custom_code',
                'products.name',
                'products.description',
                'products.sale_price',
            )
            ->orderBy('products.created_at', 'DESC')
            ->get();

        return response()->json(array(
            'products' => $products,
        ));
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
                'products.sale_price'
            )
            ->first();

        // $images = DB::table('images_products')
        //     ->where('code_product', '=', $product->code)
        //     ->get();

        return response()->json(array(
            'product' => $product,
        ));
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

    public function search($textSearch)
    {
        if ($textSearch == '') {
            $products = DB::table('products')
                ->select(
                    'products.id',
                    'products.custom_code',
                    'products.name',
                    'products.description',
                    'products.sale_price',
                )
                ->orderBy('products.created_at', 'DESC')
                ->get();
        } else {
            $products = DB::table('products')
                ->where('products.name', 'LIKE', "%{$textSearch}%")
                ->select(
                    'products.id',
                    'products.custom_code',
                    'products.name',
                    'products.description',
                    'products.sale_price',
                )
                ->orderBy('products.created_at', 'DESC')
                ->get();
        }

        return response()->json(array(
            'products' => $products
        ));
    }
}

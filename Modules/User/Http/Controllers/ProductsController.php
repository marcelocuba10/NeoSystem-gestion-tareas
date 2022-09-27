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
        $products = DB::table('products')
            ->select(
                'id',
                'name',
                'description',
                'sale_price',
                'quantity',
            )
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('user::products.index', compact('products'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function show($id)
    {
        $product = Products::find($id);
        $images = DB::table('images_products')
            ->where('code_product', '=', $product->code)
            ->get();

        return view('user::products.show', compact('product','images'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search == '') {
            $products = DB::table('products')
                ->select(
                    'id',
                    'name',
                    'sale_price',
                    'quantity',
                    'description',
                )
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        } else {
            $products = DB::table('products')
                ->where('name', 'LIKE', "%{$search}%")
                ->select(
                    'id',
                    'name',
                    'sale_price',
                    'quantity',
                    'description',
                )
                ->orderBy('created_at', 'DESC')
                ->paginate();
        }

        return view('user::products.index', compact('products', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
    }
}

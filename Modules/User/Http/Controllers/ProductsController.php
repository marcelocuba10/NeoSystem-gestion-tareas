<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $idRefCurrentUser = Auth::user()->idReference;
        $products = DB::table('products')
            ->where('idReference', '=', $idRefCurrentUser)
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
}

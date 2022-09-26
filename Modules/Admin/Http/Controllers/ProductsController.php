<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\ImagesProduct;
use Modules\Admin\Entities\Products;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin', ['except' => ['logout']]);

        $this->middleware('permission:product-sa-list|product-sa-create|product-sa-edit|product-sa-delete', ['only' => ['index']]);
        $this->middleware('permission:product-sa-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-sa-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-sa-delete', ['only' => ['destroy']]);
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

        return view('admin::products.index', compact('products'))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $code_product = $this->generateUniqueCode();
        $product = null;
        $images = null;
        $array_images = null;

        return view('admin::products.create', compact('product', 'code_product', 'images', 'array_images'));
    }

    public function store(Request $request)
    {
        $code_product = $this->generateUniqueCode();

        $request->validate([
            'name' => 'required|max:50|min:5|unique:products,name',
            'sale_price' => 'required|max:12|min:6',
            'purchase_price' => 'required|max:12|min:6',
            'description' => 'nullable|max:250|min:5',
            'quantity' => 'required|integer|between:0,9999|min:0',
            'brand' => 'nullable|max:50|min:3',
            'model' => 'nullable|max:50|min:3',
            'supplier' => 'nullable|max:50|min:3',
            'phone_supplier' => 'nullable|max:50|min:3',

            'image' => 'nullable',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($request->hasfile('image')) {

            foreach ($request->file('image') as $image) {
                $name = date('Ymd') . '-' . str_replace(' ', '-', $image->getClientOriginalName());
                $image->move(public_path('images/products'), $name);
                $data[] = $name;
            }
        }

        $input['image'] = $data;
        $input['code_product'] = $code_product;
        ImagesProduct::create($input);

        //remove the separator thousands
        $input = $request->all();
        $input['sale_price'] = str_replace('.', '', $input['sale_price']);
        $input['purchase_price'] = str_replace('.', '', $input['purchase_price']);
        $input['code'] = $code_product;
        $input['type'] = 'Equipos Potenciales';
        Products::create($input);

        return redirect()->to('/admin/products')->with('message', 'Product created successfully.');
    }

    public function generateUniqueCode()
    {
        do {
            $code_product = random_int(100000, 999999);
        } while (
            DB::table('products')->where("code", "=", $code_product)->first()
        );

        return $code_product;
    }

    public function show($id)
    {
        $product = Products::find($id);

        return view('admin::products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Products::find($id);

        $images = DB::table('images_products')
            ->where('code_product', '=', $product->code)
            ->get();

        $array_images = json_decode($images[0]->image);

        return view('admin::products.edit', compact('product', 'array_images'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50|min:5|unique:products,name,' . $id,
            'sale_price' => 'required|max:12|min:6',
            'purchase_price' => 'required|max:12|min:6',
            'description' => 'nullable|max:250|min:5',
            'quantity' => 'required|integer|between:0,9999|min:0',
            'brand' => 'nullable|max:50|min:3',
            'model' => 'nullable|max:50|min:3',
            'supplier' => 'nullable|max:50|min:3',
            'phone_supplier' => 'nullable|max:50|min:3',

            'image' => 'nullable',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $input = $request->all();

        if ($request->hasfile('image')) {

            foreach ($request->file('image') as $image) {
                $name = date('Ymd') . '-' . $image->getClientOriginalName();
                $image->move(public_path('images/products'), $name);
                $data[] = $name;
            }
        }

        $input['image'] = $data;
        ImagesProduct::create($input);


        $input['sale_price'] = str_replace('.', '', $input['sale_price']);
        $input['purchase_price'] = str_replace('.', '', $input['purchase_price']);

        $product = Products::find($id);
        $product->update($input);

        return redirect()->to('/admin/products')->with('message', 'Producto actualizado correctamente');
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

        return view('admin::products.index', compact('products', 'search'))->with('i', (request()->input('page', 1) - 1) * 10);
    }
}

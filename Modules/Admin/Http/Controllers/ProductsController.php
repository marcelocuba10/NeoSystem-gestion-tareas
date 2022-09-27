<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Entities\ImagesProduct;
use Modules\Admin\Entities\Products;
use Illuminate\Support\Facades\File;

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

    public function imageGallery($id)
    {
        $product = Products::find($id);
        $images = DB::table('images_products')
            ->where('code_product', '=', $product->code)
            ->get();

        return view('admin::products.image-gallery', compact('images', 'product'));
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
        ]);

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

    public function uploadImage(Request $request)
    {
        $request->validate([
            'filename' => 'required|unique:images_products,filename',
            'filename.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $file = $request->file('filename');
        $filename = str_replace(' ', '-', $file->getClientOriginalName());
        $file->move(public_path('/public/images/products'), $filename);

        if ($request->imageId) {
            $imageStatus = ImagesProduct::where('id', $request->imageId)
                ->update([
                    'filename' => str_replace('"', '', $filename),
                ]);

            $image_path = public_path("\public\images\products\\") . $request->oldImage;

            if (File::exists($image_path) && $request->oldImage != $filename) {
                File::delete($image_path);
            }
        } else {
            /** if the name already exists in the db  */
            $product = DB::table('images_products')
                ->where('filename', '=', $filename)
                ->get();

            if (count($product) > 0) {
                return back()->with('error', 'Ya existe una imagen con ese nombre.');
            } else {
                $product = Products::find($request->productId);
                $imageStatus = ImagesProduct::create([
                    'filename' => str_replace('"', '', $filename),
                    'code_product' => $product->code
                ]);
            }
        }

        if (!is_null($imageStatus)) {
            return back()->with("message", "Image uploaded successfully.");
        } else {
            return back()->with("error", "Failed to upload image.");
        }
    }

    public function edit($id)
    {
        $product = Products::find($id);

        $images = DB::table('images_products')
            ->where('code_product', '=', $product->code)
            ->get();

        return view('admin::products.edit', compact('product', 'images'));
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
        ]);

        $input = $request->all();
        $input['sale_price'] = str_replace('.', '', $input['sale_price']);
        $input['purchase_price'] = str_replace('.', '', $input['purchase_price']);

        $product = Products::find($id);
        $product->update($input);

        return redirect()->to('/admin/products')->with('message', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        Products::find($id)->delete();
        return redirect()->to('/admin/products')->with('message', 'Producto Eliminado Correctamente');
    }

    public function destroyImage($id)
    {
        $image = ImagesProduct::find($id);
        ImagesProduct::where('id', $id)->delete();

        $image_path = public_path("\public\images\products\\") . $image->filename;

        if (File::exists($image_path)) {
            File::delete($image_path);
        }

        return back()->with('success', 'Image removed successfully.');
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

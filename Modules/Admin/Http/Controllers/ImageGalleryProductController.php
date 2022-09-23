<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Admin\Entities\ImagesProduct;

class ImageGalleryProductController extends Controller
{
    public function index()
    {
        $images = DB::table('images_products')->get();
        return view('admin::image-gallery', compact('images'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'filename' => 'required',
            'filename.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            // 'filename' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasfile('filename')) {

            foreach ($request->file('filename') as $image) {
                $name = date('Ymd') . '-' . $image->getClientOriginalName();
                $image->move(public_path('images/products'), $name);
                $data[] = $name;
            }
        }

        $form = new ImagesProduct();
        $form->filename = json_encode($data);
        $form->save();

        // $file = $request->file('image');
        // $input['image'] = date('Ymd') . '-' . $file->getClientOriginalName();
        // $file->move(public_path('images/products'), $input['image']);

        // ImagesProduct::create($input);


        return back()->with('success', 'Image Uploaded successfully.');
    }

    public function destroy($id)
    {
        $res=ImagesProduct::where('image',$id)->delete();
        //ImagesProduct::find($id)->delete();

        // $image_path = "/images/products/filename.ext";  // Value is not URL but directory file path
        // if (File::exists($image_path)) {
        //     File::delete($image_path);
        // }

        return back()->with('success', 'Image removed successfully.');
    }
}

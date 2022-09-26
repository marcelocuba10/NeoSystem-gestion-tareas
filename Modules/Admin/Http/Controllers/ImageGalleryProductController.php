<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\Admin\Entities\ImagesProduct;
use Illuminate\Support\Facades\Storage;

class ImageGalleryProductController extends Controller
{
    public function index()
    {
        $images = DB::table('images_products')->get();
        $array_images = null;

        // dd($images);
        return view('admin::image-gallery', compact('images', 'array_images'));
    }

    public function uploadImage(Request $request)
    {

        $request->validate([
            'filename' => 'required',
            'filename.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $file = $request->file('filename');
        $filename = date('Ymd') . '-' . str_replace(' ', '-', $file->getClientOriginalName());
        $file->move(public_path('/public/images/products'), $filename);


        if ($request->imageId) {

            $imageStatus = ImagesProduct::where('id', $request->imageId)
                ->update([
                    'filename' => str_replace('"', '', $filename),
                ]);

            $image_path = public_path("\public\images\products\\") . $request->oldImage;

            if (File::exists($image_path)) {
                File::delete($image_path);
            }

        } else {
            $imageStatus = ImagesProduct::create([
                'filename' => str_replace('"', '', $filename),
                'code_product' => 111111
            ]);
        }

        if (!is_null($imageStatus)) {
            return back()->with("success", "Image uploaded successfully.");
        } else {
            return back()->with("failed", "Failed to upload image.");
        }
    }

    public function destroy($id)
    {

        $image = ImagesProduct::find($id);
        ImagesProduct::where('id', $id)->delete();

        $image_path = public_path("\public\images\products\\") . $image->filename;

        if (File::exists($image_path)) {
            File::delete($image_path);
        }

        return back()->with('success', 'Image removed successfully.');
    }
}

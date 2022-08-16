<?php

namespace App\Http\Controllers\Pages\User;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Auth;
use Illuminate\Support\Facades\File;
use Storage;

class GalleryController extends Controller
{
    // View user gallery
    public function index()
    {
        $authId = Auth::user()->id; // user id
        // user images data
        $images = Image::where('user_id', $authId)->with('user')->orderbyDesc('id')->paginate(12);
        return view('pages.user.gallery', ['images' => $images]);
    }

    // delete image
    public function deleteImage($id)
    {
        $authId = Auth::user()->id; // user id
        // check image data
        $check = Image::where('user_id', $authId)->where('id', $id)->first();
        // get image using id
        $avdata = Image::where('user_id', $authId);
        // if check data is not null
        if ($check != null) {
            // Check if image upload on server or on amazon
            if ($check->method == 1) {
                $image = str_replace(url('/') . '/', '', $check->image_path);
                if (file_exists($image)) {
                    $deleteImage = File::delete($image);
                }
            } elseif ($check->method == 2) {
                // delete image from amazon s3
                $image = pathinfo(storage_path() . $check->image_path, PATHINFO_EXTENSION);
                $awsImage = $check->image_id . '.' . $image; // file name on amazon s3
                if (Storage::disk('s3')->has($awsImage)) {
                    // Delete image from amazon s3
                    $deleteImage = Storage::disk('s3')->delete($awsImage);
                } elseif (Storage::disk('wasabi')->has($awsImage)) {
                    // Delete image from wasabi
                    $deleteImage = Storage::disk('wasabi')->delete($awsImage);
                }
            } else {
                // Error response
                return response()->json(['error' => 'Cannot find file server']);
            }
            // Delete from database
            $delete = Image::where([['user_id', $authId], ['id', $id]])->delete();
            // if deleted
            if ($delete) {
                // success response
                return response()->json([
                    'success' => 'Image deleted successfully',
                    'avdata' => $avdata->count(),
                ]);
            } else {
                // error response
                return response()->json(['error' => 'Delete error please refresh page and try again']);
            }
        } else {
            // error response
            return response()->json(['error' => 'Delete error please refresh page and try again']);
        }
    }
}

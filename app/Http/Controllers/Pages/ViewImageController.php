<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Support\Facades\File;

class ViewImageController extends Controller
{
    // View image using id
    public function index($image_id)
    {
        // if get image id
        if ($image_id) {
            // get image  data from database
            $image = Image::where('image_id', $image_id)->with('user')->first();
            // if data not null
            if ($image != null) {
                // Views + 1
                $views = $image->views + 1;
                // Update image views
                $updateView = Image::where('image_id', $image_id)->update(['views' => $views]);
                return view('pages.viewimage', ['image' => $image]);
            } else {
                // Abort 404
                return abort(404);
            }
        } else {
            // Abort 404
            return abort(404);
        }
    }

    // Download image
    public function DownloadImage($image_id)
    {
        // Get image data
        $image = Image::where('image_id', $image_id)->first();
        // if image not null
        if ($image != null) {

            $file_path = $image->image_path;
            $path = 'temp/';
            if (!File::exists($path)) {
                File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
            }
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
            );
            $fileContents = file_get_contents($file_path, false, stream_context_create($arrContextOptions));
            $ext = pathinfo($file_path, PATHINFO_EXTENSION);
            $filename = date('d-m-Y') . "_" . $image->image_id . "." . $ext;
            if (file_exists($path . $filename)) {
                $delete = File::delete($path . $filename);
            }
            $lastfile = $path . $filename;
            File::put($lastfile, $fileContents);
            $download = \Response::download($lastfile, $filename)->deleteFileAfterSend(true);
            return $download;
        } else {
            // Abort 404
            return abort(404);
        }
    }
}

<?php

namespace App\Http\Controllers\V1;

use App\Models\Album;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ImageManipulation;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ResizeImageRequest;
use App\Http\Resources\V1\ImageManipulationResource;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageManipulationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ImageManipulationResource::collection(ImageManipulation::where('user_id', Auth::user()->id)->paginate());
    }


    public function byAlbum(Album $album)
    {
        if(Auth::user()->id != $album->user_id) {
            return abort(403, "unauthorized");
        }

        return ImageManipulationResource::collection(ImageManipulation::where('album_id', $album->id)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function resize(ResizeImageRequest $request)
    {
        $all = $request->all();

        // $image UploadedFile | string
        $image = $all['image'];
        unset($all['image']);

        $data = [
            'type' => ImageManipulation::TYPE_RESIZE,
            'data' => json_encode($all),
            'user_id' => Auth::user()->id
        ];

        if(isset($all['album_id'])) {
            $album = Album::find($all['album_id']);
            if(Auth::user()->id != $album->user_id) {
                return abort(403, "unauthorized");
            }

            $data['album_id'] = $all['album_id'];
        }


        $dir = 'images/' . Str::random(). '/';
        $absolutePath = public_path($dir);
        File::makeDirectory($absolutePath);
        
        // images/random string/test.jpg
        // images/random string/test-resized.jpg

        if($image instanceof UploadedFile) {
            $data['name'] = $image->getClientOriginalName();
            // test.jpg => test-resized.jpg
            $fileName = pathinfo($data['name'], PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $originalPath = $absolutePath.$data['name'];

            $image->move($absolutePath, $data['name']);

        } else {
            $data['name'] = pathinfo($image, PATHINFO_BASENAME);
            $fileName = pathinfo($image, PATHINFO_FILENAME);
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $originalPath = $absolutePath.$data['name'];

            copy($image, $absolutePath.$data['name']);
        }
        $data['path'] = $dir.$data['name'];

        $w = $all['w'];
        $h = $all['h'] ?? false;

        list($width, $height, $image) = $this->getImageWidthAndHeight($w, $h, $originalPath);

        $resizedFilename = $fileName . '-resized.' . $extension;

        $image->resize($width, $height)->save($absolutePath.$resizedFilename);
        $data['output_path'] = $dir.$resizedFilename;

        $imageManipulation = ImageManipulation::create($data);

        return new ImageManipulationResource($imageManipulation);


    }

    /**
     * Display the specified resource.
     */
    public function show(ImageManipulation $image)
    {
        if(Auth::user()->id != $image->user_id) {
            return abort(403, "unauthorized");
        }

        return new ImageManipulationResource($image);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ImageManipulation $image)
    {
        if(Auth::user()->id != $image->user_id) {
            return abort(403, "unauthorized");
        }

        $image->delete();
        return response("", 204);
    }


    public function getImageWidthAndHeight($w, $h, $originalPath)
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($originalPath);

        $originalWidth = $image->width();
        $originalHeight = $image->height();

        if(str_ends_with($w, '%')) {
            $ratioW = (float)str_replace('%', '', $w);
            $ratioH = $h ? (float)str_replace('%', '', $h) : $ratioW;

            $newWidth = $originalWidth * $ratioW / 100;
            $newHeight = $originalHeight * $ratioH / 100;

        } else {    
            $newWidth = (float)$w;
            $newHeight = $h ? (float)$h : $originalHeight * $newWidth/$originalWidth;
        }
        
        return [$newWidth, $newHeight, $image];
    }
}

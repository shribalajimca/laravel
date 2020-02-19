<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Image;
use File;
class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('image');
    }
   // .....

    public function store(Request $request)
{
    if($request->hasFile('profile_image')) {
        //get filename with extension
        $filenamewithextension = $request->file('profile_image')->getClientOriginalName();
 
        //get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
 
        //get file extension
        $extension = $request->file('profile_image')->getClientOriginalExtension();
 
        //filename to store
        $filenametostore = $filename.'_'.time().'.'.$extension;
        
        //Upload File
        $request->file('profile_image')->storeAs('public/profile_images', $filenametostore);
 
        if(!file_exists(public_path('storage/profile_images/crop'))) {
            mkdir(public_path('storage/profile_images/crop'), 0755);
        }
 
        // crop image
        $img = Image::make(public_path('storage/profile_images/'.$filenametostore));
        $croppath = public_path('storage/profile_images/crop/'.$filenametostore);
 
        //$img->crop($request->input('w'), $request->input('h'), $request->input('x1'), $request->input('y1'));
        Image::make($img)->resize(200, 200)->save($croppath);
        //$img->save($croppath);
 
        // you can save crop image path below in database
        $path = asset('storage/profile_images/crop/'.$filenametostore);
 
        return redirect('image')->with(['success' => "Image cropped successfully.", 'path' => $path]);
        //$esss = $this->displayImage($filenametostore);
    }
}

public function displayImage($filenametostore)

{

// dd($filenametostore);
// die();
    $path = public_path('storage/profile_images/crop/' . $filenametostore);

  

    if (!File::exists($path)) {

        abort(404);

    }

   

    $file = File::get($path);

    $type = File::mimeType($path);

  

    $response = Response::make($file, 200);

    $response->header("Content-Type", $type);

  

    return $response;

}

}
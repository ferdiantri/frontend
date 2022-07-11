<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Banner;
use Validator;
use Intervention\Image\ImageManager;
use Image;

class BannerController extends Controller
{
    public function tambahBanner(Request $request){
        $validator = Validator::make($request->all(), [
            'gambar' => 'required',
            'content_banner' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };

        $manager = new ImageManager(['driver' => 'imagick']);
        $gambar = $request->file('gambar');
        $image = $manager->make($gambar);
        $image_path = public_path('/data_gambar/');
        if (!file_exists($image_path)) {
            mkdir($image_path, 666, true);
        }
        $image_name = time().'.'.$gambar->getClientOriginalExtension();
        $image->resize(700, 300, function($constraint) {
            $constraint->aspectRatio();
        }); 
        $image->save($image_path.$image_name);
        $id_banner = 'KBAN'.date('Ymdhis').rand(1, 10);
        $banner = Banner::create([
            'gambar' => ('data_gambar/'.$image_name),
            'id_banner' => $id_banner,
            'content_banner' => $request['content_banner'],
        ]);
        if($banner){
            $response = $banner;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
    public function getBanner(Request $request){
        $banner = Banner::all();
        if($banner){
            $response = $banner;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
    public function detailBanner(Request $request){
        $banner = Banner::where('id_banner', $request['id_banner'])->get();
        if($banner){
            $response = $banner;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
}
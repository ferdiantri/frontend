<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Barang;
use Intervention\Image\ImageManager;
use Image;
use Validator;
use Auth;


class BarangController extends Controller
{
    public function tambahBarang(Request $request){
        $validator = Validator::make($request->all(), [
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'nama_barang' => 'required|string',
            'ram' => 'required|integer',
            'internal' => 'required|integer',
            'warna' => 'required|string',
            'kamera_depan' => 'required|string',
            'kamera_belakang' => 'required|string',
            'layar' => 'required|string',
            'chipset' => 'required|string',
            'baterai' => 'required|string',
            'email' => 'required|email'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = Barang::select('nama_barang', 'ram', 'internal', 'warna')
        ->where([['nama_barang', $request['nama_barang']],['warna', $request['warna']],['ram', $request['ram']],['internal', $request['internal']]])
        ->get();
        
        if(count($barang) >= 1){
            return response()->json(['error' => 'Terdapat Kesamaan Nama Barang, Ram, Internal Dan Warna Pada Barang Lain'], 200);
        }
        else{
            $manager = new ImageManager(['driver' => 'imagick']);
        $gambar = $request->file('gambar');
        $image = $manager->make($gambar);
        $image_path = public_path('/data_gambar/');
        if (!file_exists($image_path)) {
            mkdir($image_path, 666, true);
        }
        $image_name = time().'.'.$gambar->getClientOriginalExtension();
        $image->resize(500, 500, function($constraint) {
            $constraint->aspectRatio();
        }); 
        $image->save($image_path.$image_name);
        $barang = Barang::create([
            'id_barang' => 'KB'.date('Ymdhis'),
            'gambar' => ('data_gambar/'.$image_name),
            'nama_barang' => ucwords($request['nama_barang']),
            'ram' => $request['ram'],
            'internal' => $request['internal'],
            'warna' => ucwords($request['warna']),
            'kamera_depan' => $request['kamera_depan'],
            'kamera_belakang' => $request['kamera_belakang'],
            'layar' => $request['layar'],
            'chipset' => $request['chipset'],
            'baterai' => $request['baterai'],
            'harga' => '0',
            'stok_barang' => '0',
            'terjual' => '0',
            'status' => 'Belum Dijual!',
            'email' => $request['email'],
        ]);
            return response()->json(['success' => 'Barang Berhasil Ditambahkan'], 200);
        }
    }
    public function barang(Request $request){
        $barang = Barang::orderBy('stok_barang', 'DESC')->get();
        $response = $barang;
        return response()->json($response, 200);
    }
    public function detailBarang(Request $request){
        $detailBarang = Barang::select('*')->where('id_barang', $request['id_barang'])->get();
        $response = $detailBarang;
        return response()->json($response, 200);
    }
    public function detailBarang_admin(Request $request){
        $detailBarang = Barang::select('*')->where('id_barang', $request['id_barang'])->get();
        $response = $detailBarang;
        return response()->json($response, 200);
    }
    public function cariBarang(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $cariBarang = Barang::where('nama_barang', 'like', $request['nama_barang'] . '%')->get();
        if(count($cariBarang) <= 0){
            return response()->json(['error' => ['Barang Tidak Ditemukan']], 200);
        }
        else{
            $response = $cariBarang;
            return response()->json($response, 200);
        }
    }
    public function cekNamaBarang(Request $request){
        $barang = Barang::select('gambar','id_barang','nama_barang','warna','ram','internal','stok_barang')->where('nama_barang', 'like', $request['nama_barang'] . '%')->get();
        $response = $barang;
        return response()->json($response, 200);
    }
    public function cekBarang(Request $request){
        $barang = Barang::select('id_barang','nama_barang','warna','ram','internal','stok_barang','harga')->where('stok_barang', '>', 0)->where('nama_barang', 'like', $request['nama_barang'] . '%')->get();
        $response = $barang;
        return response()->json($response, 200);
    }
    public function cekRamBarang(Request $request){
        $barang = Barang::select('ram')->where('nama_barang', $request['nama_barang'])->get();
        $response = $barang;
        return response()->json($response, 200);
    }
    public function cekInternalBarang(Request $request){
        $barang = Barang::select('internal')->where('nama_barang', $request['nama_barang'])->where('ram', $request['ram'])->get();
        $response = $barang;
        return response()->json($response, 200);
    }
    public function cekWarnaBarang(Request $request){
        $barang = Barang::select('warna')->where('nama_barang', $request['nama_barang'])->where('ram', $request['ram'])->where('internal', $request['internal'])->get();
        $response = $barang;
        return response()->json($response, 200);
    }
    public function cekKesamaanBarang(Request $request){
        $barang = Barang::select('nama_barang', 'ram', 'internal', 'warna')
        ->where([['nama_barang', $request['nama_barang']],['warna', $request['warna']],['ram', $request['ram']],['internal', $request['internal']]])
        ->get();
        if(count($barang) >= 1){
            return response()->json(['error' => 'Terdapat Kesamaan Data'], 200);
        }
        else{
            return response()->json(['success' => $barang], 200);
        }
    }

}
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Alamat;
use Validator;

class AlamatController extends Controller
{
    public function tambahAlamat(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'nomor_telepon' => 'required|string',
            'nama_penerima' => 'required|string',
            'alamat' => 'required|string',
            'province_id' => 'required|integer',
            'city_id' => 'required|integer',
            'provinsi' => 'required|string',
            'kecamatan' => 'required|string',
            'kabupaten' => 'required|string',
            'kode_pos' => 'required|integer',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $alamat = Alamat::create([
            'id_alamat' => 'KA'.date('Ymdhis').rand(1, 10),
            'email' => $request['email'],
            'nomor_telepon' => $request['nomor_telepon'],
            'alamat' => $request['alamat'],
            'nama_penerima' => $request['nama_penerima'],
            'province_id' => $request['province_id'],
            'city_id' => $request['city_id'],
            'provinsi' => $request['provinsi'],
            'kecamatan' => $request['kecamatan'],
            'kabupaten' => $request['kabupaten'],
            'kode_pos' => $request['kode_pos'],
        ]);
        if($alamat){
            $response = $alamat;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
    public function alamat(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $alamat = Alamat::select('*')->where('email', $request['email'])->get();
        if(count($alamat) <= 0 ){
            return response()->json(['error' => ['Alamat Tidak Ditemukan.']], 200);
        }
        else{
            $response['success'] = $alamat;
            return response()->json($response, 200);
        }
    }
    public function ubahAlamat(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'nomor_telepon' => 'required|string',
            'nama_penerima' => 'required|string',
            'alamat' => 'required|string',
            'province_id' => 'required|integer',
            'city_id' => 'required|integer',
            'provinsi' => 'required|string',
            'kecamatan' => 'required|string',
            'kabupaten' => 'required|string',
            'kode_pos' => 'required|integer',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $ubah_alamat = Alamat::where('email', $request['email'])->update(['nama_penerima' => $request['nama_penerima'],
         'nomor_telepon' => $request['nomor_telepon'], 'alamat' => $request['alamat'], 'province_id' => $request['province_id'],
         'city_id' => $request['city_id'], 'provinsi' => $request['provinsi'], 'kecamatan' => $request['kecamatan'], 
         'kabupaten' => $request['kabupaten'], 'kode_pos' => $request['kode_pos']]);
         
        $response = $ubah_alamat;
            return response()->json($response, 200);
    }
}
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Imei;
use Validator;

class ImeiController extends Controller
{
    public function tambahImei(Request $request){
        $alamat = Imei::create([
            'id_penjualan' => $request['id_penjualan'],
            'imei' => $request['imei'],
        ]);
        if($alamat){
            $response = $alamat;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
    
}
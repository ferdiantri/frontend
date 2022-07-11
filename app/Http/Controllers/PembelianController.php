<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pembelian;
use Validator;
use App\Models\Barang;
use Auth;
use DB;

class PembelianController extends Controller
{
    public function tambahPembelian(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_barang' => 'required|string',
            'ram' => 'required|integer',
            'internal' => 'required|integer',
            'warna' => 'required|string',
            'jumlah_barang' => 'required|integer',
            'harga_beli' => 'required|integer',
            'harga_jual' => 'required|integer',
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };

        
        $cek_barang = Barang::where('id_barang', $request['id_barang'])->get();
        foreach($cek_barang as $cb){
            $stok_barang = $cb['stok_barang']+$request['jumlah_barang'];
        };
        $pembelian = Pembelian::create([
            'id_pembelian' => 'KP'.date('sYmdhs').rand(1, 1000),
            'id_barang' => $request['id_barang'],
            'tanggal_pembelian' => date('Y-m-d H:i:s'),
            'nama_barang' => ucwords($request['nama_barang']),
            'ram' => $request['ram'],
            'internal' => $request['internal'],
            'warna' => $request['warna'],
            'jumlah_barang' => $request['jumlah_barang'],
            'harga_beli' => $request['harga_beli'],
            'harga_jual' => $request['harga_jual'],
            'total_harga' => $request['harga_beli']*$request['jumlah_barang'],
            'email' => $request['email']
        ]);
        $update_barang = Barang::where('id_barang', $request['id_barang'])->update(['harga' => $request['harga_jual'], 'stok_barang' => $stok_barang]);
        $response['pembelian'] = $pembelian;
        $response['update_barang'] = $update_barang;
        $response['sucsess'] = true;
        return response()->json($response, 200);
    }
    
    public function pembelian(Request $request){
        $pembelian = Pembelian::all();
        if($pembelian){
            $response = $pembelian;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Pembelian Kosong']], 200);
        }
    }
    public function totalPembelian(Request $request){
        $pembelian = Pembelian::select(DB::raw("SUM(total_harga) as total_harga"))->whereMonth('pembelian.tanggal_pembelian', '=', $request['bulan'])
        ->whereYear('pembelian.tanggal_pembelian', $request['tahun'])->get();
        if($pembelian){
            $response['barang'] = $pembelian;
            return response()->json($response, 200);
        }
        elseif($pembelian == null){
            $response['barang'] = 0;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Pembelian Kosong']], 200);
        }
    }
}
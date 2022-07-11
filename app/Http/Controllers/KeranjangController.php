<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Keranjang;
use Validator;
use App\Models\Barang;

class KeranjangController extends Controller
{
    public function tambahKeranjang(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'id_barang' => 'required|string',
            'jumlah_barang' => 'required|integer',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };

        $cek_barang = Barang::select('*')->where('id_barang', $request['id_barang'])->get();
        $cek_keranjang = Keranjang::select('*')->where('email', $request['email'])->where('id_barang', $request['id_barang'])->get();
        
        if(count($cek_keranjang) <= 0){
            foreach($cek_barang as $cb){
                $stok = $cb['stok_barang'];
            }
            if(count($cek_barang) <= 0){
                return response()->json(['error' => ['Barang Tidak Ditemukan.']], 200);
            }
            elseif($stok <= 0){
                return response()->json(['error' => ['Stok Barang Tidak Mencukupi']], 200);
            }
            else{
                $keranjang = Keranjang::create([
                    'email' => $request['email'],
                    'id_barang' => $request['id_barang'],
                    'jumlah_barang' => $request['jumlah_barang'],
                ]);
                $response = $keranjang;
            }
        }
        else{
            foreach($cek_barang as $cb){
                $stok = $cb['stok_barang'];
            }
            foreach($cek_keranjang as $ck){
                $jumlah_barang = $ck['jumlah_barang'];
            }
            if($stok < $request['jumlah_barang']+$jumlah_barang){
                return response()->json(['error' => ['Stok Barang Tidak Mencukupi.']], 200);
            }
            elseif(count($cek_keranjang) > 0){
                $update_keranjang = Keranjang::where('id_barang', $request['id_barang'])->update(['jumlah_barang' => $request['jumlah_barang']+$jumlah_barang]);
            }
            elseif($stok < 0){
                return response()->json(['error' => ['Barang Telah Habis.']], 200);
            }
        }
        return response()->json(['success' => ['Barang Telah Ditambahkan.']], 200);
    }
    public function keranjang(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $keranjang = Keranjang::select('*')->where('email', $request['email'])->get();
        foreach($keranjang as $cart){
            $id_barang = $cart['id_barang'];
            $jumlah_barang = $cart['jumlah_barang'];
        }
        if(count($keranjang) <= 0){
            return response()->json(['error' => ['Keranjangmu Kosong nih.']], 200);
        }
        
        $detail_keranjang = Keranjang::join('barang', 'barang.id_barang', '=', 'keranjang.id_barang')->where('keranjang.email', $request['email'])->get();
        
        foreach($detail_keranjang as $det_cart){
            $gambar = $det_cart['gambar'];
            $nama_barang = $det_cart['nama_barang'];
            $ram = $det_cart['ram'];
            $internal = $det_cart['internal'];
            $warna = $det_cart['warna'];
            $harga = $det_cart['harga'];
            $stok = $det_cart['stok'];
        }
        if($stok <= 0){
            Keranjang::join('barang', 'barang.id_barang', '=', 'keranjang.id_barang')
            ->where('keranjang.email', $request['email'])
            ->where('keranjang.id_barang', $id_barang)->where('barang.stok_barang', '<=', 0)->delete();
        }
        $res = ([[
            'id_barang' => $id_barang,
            'jumlah_barang' => $jumlah_barang,
            'gambar' => $gambar,
            'nama_barang' => $nama_barang,
            'ram' => $ram,
            'internal' => $internal,
            'warna' => $warna,
            'harga' => $harga
        ]]);

        $response = $res;
        return response()->json($detail_keranjang, 200);
    }
    public function deleteKeranjang(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'id_barang' => 'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $delete_keranjang = Keranjang::where('email', $request['email'])->where('id_barang', $request['id_barang'])->delete();
        if($delete_keranjang == 1){
            $keranjang = Keranjang::select('*')->where('email', $request['email'])->get();
            if(count($keranjang) <= 0){
                return response()->json(['success' => ['Berhasil']], 200);
            }
        }
        if($delete_keranjang == 0){
            return response()->json(['success' => ['Tidak Ada']], 200);
        }
    }
}
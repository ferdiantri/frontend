<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\PenjualanOffline;
use App\Models\Barang;
use Validator;
use Auth;
use DB;

class PenjualanOfflineController extends Controller
{
    // USER
    public function tambahPenjualanOffline_admin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_barang' => 'required',
            'nama_pembeli' => 'required',
            'alamat' => 'required',
            'nomor_telepon' => 'required',
            'id_barang' => 'required',
            'jumlah_barang' => 'required',
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        }; 
        date_default_timezone_set("Asia/Jakarta");
        $id_penjualan = 'KP'.date('Ymdhis').rand(0, 100);
            foreach($request['id_barang'] as $key => $value){
                $detailBarang = Barang::select('*')->where('id_barang', $request['id_barang'][$key])->get();
                foreach($detailBarang as $db){
                    $barang[] = $db['nama_barang'];
                    $stok_barang[] = $db['stok_barang'];
                    $harga[] = $db['harga'];
                    $terjual[] = $db['terjual'];
                }
                $kurang_stok_barang = Barang::where('id_barang', $request['id_barang'][$key])
                ->update(['stok_barang' => $stok_barang[$key] - $request['jumlah_barang'][$key], 
                'terjual' => $terjual[$key] + $request['jumlah_barang'][$key]]);
                $penjualan_offline[] = PenjualanOffline::create([
                    'id_penjualan_offline' => $id_penjualan,
                    'tanggal_penjualan' => date('Y-m-d H:i:s'),
                    'nama_pembeli' => $request['nama_pembeli'],
                    'alamat' => $request['alamat'],
                    'nomor_telepon' => $request['nomor_telepon'],
                    'id_barang' => $request['id_barang'][$key],
                    'jumlah_barang' => $request['jumlah_barang'][$key],
                    'harga' => $harga[$key],
                    'total_harga' => $harga[$key] * $request['jumlah_barang'][$key],
                    'email' => $request['email']
                ]);
            }
            if($penjualan_offline){
                $response['penjualan'] = $penjualan_offline;
                return response()->json($response, 200);
            }
            else{
                return response()->json(['error' => ['Gagal']], 200);
            }
            
        }
        public function penjualanOffline_admin(Request $request){
            $barang = [];
            penjualanOffline::all()
            ->groupBy('id_penjualan_offline')
            ->each(function ($item, $key) use (&$barang) {
                $temp = [];
                $temp['id_penjualan_offline'] = $key;
                foreach($item as $b){
                    $total_harga = PenjualanOffline::where('id_penjualan_offline', $key)->sum('total_harga');
                    $alamat = $b['alamat'];
                    $nama_pembeli = $b['nama_pembeli'];
                    $nomor_telepon = $b['nomor_telepon'];
                    $tanggal_penjualan = $b['tanggal_penjualan'];
                }
                $temp['tanggal_penjualan'] = $tanggal_penjualan;
                $temp['nama_pembeli'] = $nama_pembeli;
                $temp['nomor_telepon'] = $nomor_telepon;
                $temp['total_harga'] = $total_harga;
                $temp['barang'] = $item->transform(function ($i, $k) {
                    unset($i['tanggal_penjualan']);
                    unset($i['id_penjualan_offline']);
                    unset($i['nama_pembeli']);
                    unset($i['nomor_telepon']);
                    unset($i['email']);
                    return $i;
                })->all();
                $barang[] = $temp;
            });
            $response['barang'] = $barang;
    
            return response()->json($response, 200);
        }
    }
    

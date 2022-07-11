<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Models\Barang;
use Validator;

class NotifikasiController extends Controller
{
    public function tambahNotifikasi(Request $request){
        $barang = Barang::where('id_barang', $request['id_barang'])->get();
        foreach($barang as $b){
            $nama_barang = $b['nama_barang'].' '.$b['ram'].'/'.$b['internal'].' - '.$b['warna'];
        }
        $cek_notifikasi = Notifikasi::where('pesan', $nama_barang.' ('.$request['id_barang'].') Stok Hampir Habis')
        ->where('status', 'Belum Dilihat')->get();
        if(count($cek_notifikasi) >= 1){
            return response()->json(['error' => ['Notifikasi Sudah Tersedia']], 200);
        }
        else{
            $notifikasi = Notifikasi::create([
                'id_notifikasi' => 'KN'.date('sYmdhs').rand(0, 100),
                'tanggal' => date('Y-m-d H:i:s'),
                'pesan' => $nama_barang.' ('.$request['id_barang'].') Stok Hampir Habis',
                'status' => 'Belum Dilihat'
            ]);
            if($notifikasi){

                $response = $notifikasi;
                return response()->json($response, 200);
            }
            else{
                return response()->json(['error' => ['Gagal.']], 200);
            }
        }
    }
    public function notifikasi(Request $request){
        $notifikasi = Notifikasi::where('status', 'Belum Dilihat')->get();
        if($notifikasi){
            $response['notifikasi'] = $notifikasi;
            $response['badge'] = count($notifikasi);
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
    public function updateNotifikasi(Request $request){
        $update_notifikasi = Notifikasi::where('id_notifikasi', $request['id_notifikasi'])->update(['status' => 'Dibaca']);
        if($update_notifikasi){
            return response()->json(['success' => ['Berhasil']], 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }

}
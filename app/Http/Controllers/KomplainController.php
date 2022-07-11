<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Komplain;
use App\Models\Penjualan;
use App\Models\LogPenjualan;
use Validator;

class KomplainController extends Controller
{
    public function tambahKomplain(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
            'masalah' => 'required',
            'deskripsi_masalah' => 'required',
            'link_youtube' => 'required',
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $penjualan = Penjualan::where('id_penjualan', $request['id_penjualan'])->update(['status' => 'COMPLAINT']);
        $log_penjualan = LogPenjualan::create([
            'id_penjualan' => $request['id_penjualan'],
            'status_log' => 'Pesanan dikomplain',
            'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
        ]);
        
        $komplain = Komplain::create([
            'id_komplain' => 'KKOM'.date('sYmdhs').rand(1, 10),
            'id_penjualan' => $request['id_penjualan'],
            'masalah' => $request['masalah'],
            'deskripsi_masalah' => $request['deskripsi_masalah'],
            'link_youtube' => $request['link_youtube'],
            'email' => $request['email'],
            'status' => 'AKTIF',
            'tanggal_komplain' => date('Y-m-d H:i:s'),
            'tanggapan_admin' => '',
            'email_admin' => ''
        ]);
        if($komplain){
            $response = $komplain;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
    public function komplain(Request $request){
        $komplain = Komplain::where('id_penjualan', $request['id_penjualan'])->orderBy('tanggal_komplain', 'desc')->first();
        $response = $komplain;
        return response()->json($response, 200);
    }
    public function tanggapanAdmin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
            'tanggapan_admin' => 'required',
            'email_admin' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $komplain = Komplain::where('id_penjualan', $request['id_penjualan'])->update(['tanggapan_admin' => $request['tanggapan_admin'], 'email_admin' => $request['email_admin']]);
        if($komplain >= 1){
            return response()->json(['success' => ['Berhasil']], 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
    public function komplainSelesai(Request $request){
        $penjualan = Penjualan::where('id_penjualan', $request['id_penjualan'])->update(['status' => 'DELIVERED']);
        $komplain = Komplain::where('id_penjualan', $request['id_penjualan'])->update(['status' => 'CLOSED']);
        $log_penjualan = LogPenjualan::create([
            'id_penjualan' => $request['id_penjualan'],
            'status_log' => 'Komplain ditutup',
            'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
        ]);
        if($komplain >= 1){
            return response()->json(['success' => ['Berhasil']], 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
}
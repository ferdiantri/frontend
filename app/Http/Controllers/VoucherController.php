<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Penjualan;
use Validator;

class VoucherController extends Controller
{
    public function tambahVoucher(Request $request){
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string',
            'min_pembelian' => 'required|integer',
            'potongan' => 'required|integer',
            'kuota' => 'required|integer',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'email' => 'required|email',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $voucher = Voucher::create([
            'id_voucher' => 'KV'.date('Ymdhis').rand(1,10),
            'kode' => $request['kode'],
            'min_pembelian' => $request['min_pembelian'],
            'potongan' => $request['potongan'],
            'kuota' => $request['kuota'],
            'tanggal_mulai' => $request['tanggal_mulai'],
            'tanggal_selesai' => $request['tanggal_selesai'],
            'email' => $request['email'],
        ]);
        if($voucher){
            $response = $voucher;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Gagal.']], 200);
        }
    }
    public function voucher(Request $request){
        $validator = Validator::make($request->all(), [
            'kode' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json(['error' => [$validator->errors()->all()]]);
        };
        $voucher = Voucher::select('*')->where('kode', $request['kode'])->get();
        if(count($voucher)<=0){
            return response()->json(['error' => ['Kode Voucher Tidak Ditemukan']], 200);
        }
        foreach($voucher as $vc){
            $kuota = $vc['kuota'];
            $tanggal_mulai = $vc['tanggal_mulai'];
            $tanggal_selesai = $vc['tanggal_selesai'];
        }
        date_default_timezone_set('Asia/Jakarta');
        $tanggal_sekarang = date('Y-m-d');
        if($kuota <= 0){
            return response()->json(['error' => ['Kode Voucher Telah Habis']], 200);
        }   
        elseif($tanggal_mulai > $tanggal_sekarang){
            $response = $voucher;
            return response()->json(['error' => ['Kode Voucher Belum Dimulai']], 200);
        }
        elseif($tanggal_selesai <= $tanggal_sekarang){
            $response = $voucher;
            return response()->json(['error' => ['Kode Voucher Telah Berakhir']], 200);
        }
        elseif($tanggal_mulai <= $tanggal_sekarang AND $tanggal_selesai >= $tanggal_sekarang){
            $response = $voucher;
            return response()->json($response, 200);
        }
    }
    public function voucherAll(Request $request){
        $voucher = Voucher::all();
        if($voucher){
            $response = $voucher;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Sistem Error']], 200);
        }
    }
    public function detailPenggunaanVoucher(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran',
        'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.id_voucher',
        'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan',
        'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', '!=' , 'EXPIRED')
        ->where('penjualan.id_voucher', $request['id_voucher'])
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            $temp['id_penjualan'] = $key;
            foreach($item as $b){
                $total_harga = $b['total_harga'];
                $link_invoie = $b['link_invoice'];
                $id_pembayaran = $b['id_pembayaran'];
                $status_db = $b['status'];
                $tanggal_penjualan = $b['tanggal_penjualan'];
                $email = $b['email'];
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['total_harga'] = $total_harga;
            $temp['status'] = $status_db;
            $temp['link_invoice'] = $link_invoie;
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
}
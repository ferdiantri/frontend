<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Pembayaran;
use Validator;
use App\Models\Barang;
use Auth;
use DB;
use Xendit\Xendit;
Xendit::setApiKey('xnd_development_9OgIWa7nlFShkJIaNgjt0kyJ93rSLWLISVeuO0tzfhKsctEZc5LZMcHKY1nOQ');

class PenjualanController extends Controller
{
    public function tambahPenjualan(Request $request){
        $validator = Validator::make($request->all(), [
            'id_barang' => 'required',
            'id_alamat' => 'required',
            'jasa_pengiriman' => 'required',
            'ongkir' => 'required',
            'potongan' => 'required',
            'id_voucher' => 'required',
            'total_harga' => 'required',
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $detailBarang = Barang::select('*')->whereIn('id_barang', $request['id_barang'])->get();
        foreach($detailBarang as $db){
            $barang[] = $db['nama_barang'];
        }

        date_default_timezone_set("Asia/Jakarta");
        $id_penjualan = 'KP'.date('Ymdhis').rand(0, 100);
        $params = ([ 
            'external_id' => $id_penjualan,
            'payer_email' => $request['email'],
            'description' => $request['email'] .' order '. implode(", ", $barang),
            'amount' => $request['total_harga']
        ]);
        $createInvoice = \Xendit\Invoice::create($params);
        if($createInvoice){
            date_default_timezone_set("Asia/Jakarta");
            $penjualan = Penjualan::create([
                'id_penjualan' => $id_penjualan,
                'id_barang' => implode(",",$request['id_barang']),
                'id_alamat' => $request['id_alamat'],
                'tanggal_penjualan' => date('Y-m-d h:i:s'),
                'harga_barang' => implode(",", $request['harga_barang']),
                'jumlah_barang' => implode(",", $request['jumlah_barang']),
                'id_voucher' => $request['id_voucher'],
                'potongan' => $request['potongan'],
                'total_harga' => $request['total_harga'],
                'jasa_pengiriman' => $request['jasa_pengiriman'],
                'ongkir' => $request['ongkir'],
                'status' => $createInvoice['status'],
                'nomor_resi' => 0,
                'email' => $request['email']
            ]);
            $pembayaran = Pembayaran::create([
                'id_pembayaran' => $createInvoice['id'],
                'id_penjualan' => $id_penjualan,
                'link_invoice' => $createInvoice['invoice_url'],
                'email' => $request['email']
            ]);
            $response['invoice'] = $createInvoice;
            $response['penjualan'] = $penjualan;
            $response['detail_barang'] = $barang;
            return response()->json($response, 200);
        }
        else{
            return response()->json(['error' => ['Gagal']], 200);
        }
    }
    public function penjualanBelumDibayar(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $penjualanPending = Penjualan::select('*')->where('email', $request['email'])->get();
        $id_barang = [];
        foreach($penjualanPending as $list){
            $id_barang = $list['id_barang'];
        }
        $barang = Barang::select('*')->where('id_barang', $id_barang)->get();
        $response['barang'] = $barang;
        $response['id_barang'] = $id_barang;
        $response['penjualan'] = $penjualanPending;
        return response()->json($response, 200);
    }
}
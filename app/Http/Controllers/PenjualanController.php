<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Pembayaran;
use App\Models\Voucher;
use App\Models\LogPenjualan;
use App\Models\Barang;
use App\Models\Keranjang;
use App\Models\Imei;
use App\Models\Komplain;
use Validator;
use Auth;
use DB;
use Xendit\Xendit;
Xendit::setApiKey('xnd_development_9OgIWa7nlFShkJIaNgjt0kyJ93rSLWLISVeuO0tzfhKsctEZc5LZMcHKY1nOQ');

class PenjualanController extends Controller
{
    // USER
    public function tambahPenjualan_user(Request $request){
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
        $voucher = Voucher::where('id_voucher', $request['id_voucher'])->get();
        foreach($voucher as $kv){
            $kuota = $kv['kuota'];
        }
        if($request['id_voucher'] == 0){  
            $detailBarang = Barang::select('*')->whereIn('id_barang', $request['id_barang'])->get();
            Keranjang::where('id_barang', $request['id_barang'])->where('email', $request['email'])->delete();
            foreach($detailBarang as $db){
                $barang[] = $db['nama_barang'];
                $stok_barang[] = $db['stok_barang'];
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
            $date = date('Y-m-d h:i:s');
            if($createInvoice){
                date_default_timezone_set("Asia/Jakarta");
                foreach($request['id_barang'] as $key => $value){
                    Keranjang::where('id_barang', $request['id_barang'][$key])->where('email', $request['email'])->delete();
                    Barang::where('id_barang', $request['id_barang'][$key])->update(['stok_barang' => $stok_barang[$key] - $request['jumlah_barang'][$key]]);
                    $penjualan[] = Penjualan::create([
                        'id_penjualan' => $id_penjualan,
                        'id_barang' => $request['id_barang'][$key],
                        'id_alamat' => $request['id_alamat'],
                        'tanggal_penjualan' => $date,
                        'harga_barang' => $request['harga_barang'][$key],
                        'jumlah_barang' => $request['jumlah_barang'][$key],
                        'id_voucher' => $request['id_voucher'],
                        'potongan' => $request['potongan'],
                        'total_harga' => $request['total_harga'],
                        'jasa_pengiriman' => $request['jasa_pengiriman'],
                        'ongkir' => $request['ongkir'],
                        'status' => $createInvoice['status'],
                        'nomor_resi' => 0,
                        'email' => $request['email']
                    ]);
                }   
                $pembayaran = Pembayaran::create([
                    'id_pembayaran' => $createInvoice['id'],
                    'id_penjualan' => $id_penjualan,
                    'link_invoice' => $createInvoice['invoice_url'],
                    'email' => $request['email']
                ]);
                $log_penjualan = LogPenjualan::create([
                    'id_penjualan' => $id_penjualan,
                    'status_log' => 'Pesanan dibuat',
                    'tanggal_penjualan_log' => $date,
                ]);
                $response['invoice'] = $createInvoice;
                $response['penjualan'] = $penjualan;
                $response['detail_barang'] = $barang;
                $response['log_penjualan'] = $log_penjualan;
                return response()->json($response, 200);
            }
            else{
                return response()->json(['error' => ['Gagal']], 200);
            }
        }
        elseif(count($voucher) >= 1 && $kuota <= 0){
            return response()->json(['error' => 'Voucher Habis'], 200);
        }
        elseif(count($voucher) >= 1 && $kuota >= 0){
            $kurang_kuota_voucher = Voucher::where('id_voucher', $request['id_voucher'])->update(['kuota' => $kuota - 1]);
            $detailBarang = Barang::select('*')->whereIn('id_barang', $request['id_barang'])->get();
            foreach($detailBarang as $db){
                $barang[] = $db['nama_barang'];
                $stok_barang[] = $db['stok_barang'];
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
            $date = date('Y-m-d h:i:s');
            if($createInvoice){
                date_default_timezone_set("Asia/Jakarta");
                foreach($request['id_barang'] as $key => $value){
                    $kurang_stok_barang = Barang::where('id_barang', $request['id_barang'][$key])->update(['stok_barang' => $stok_barang[$key] - $request['jumlah_barang'][$key]]);
                    $penjualan[] = Penjualan::create([
                        'id_penjualan' => $id_penjualan,
                        'id_barang' => $request['id_barang'][$key],
                        'id_alamat' => $request['id_alamat'],
                        'tanggal_penjualan' => $date,
                        'harga_barang' => $request['harga_barang'][$key],
                        'jumlah_barang' => $request['jumlah_barang'][$key],
                        'id_voucher' => $request['id_voucher'],
                        'potongan' => $request['potongan'],
                        'total_harga' => $request['total_harga'],
                        'jasa_pengiriman' => $request['jasa_pengiriman'],
                        'ongkir' => $request['ongkir'],
                        'status' => $createInvoice['status'],
                        'nomor_resi' => 0,
                        'email' => $request['email']
                    ]);
                }   
                $pembayaran = Pembayaran::create([
                    'id_pembayaran' => $createInvoice['id'],
                    'id_penjualan' => $id_penjualan,
                    'link_invoice' => $createInvoice['invoice_url'],
                    'email' => $request['email']
                ]);
                $log_penjualan = LogPenjualan::create([
                    'id_penjualan' => $id_penjualan,
                    'status_log' => 'Pesanan dibuat',
                    'tanggal_penjualan_log' => $date,
                ]);
                $response['invoice'] = $createInvoice;
                $response['penjualan'] = $penjualan;
                $response['detail_barang'] = $barang;
                $response['log_penjualan'] = $log_penjualan;
                $response['voucher'] = $kurang_kuota_voucher;
                return response()->json($response, 200);
            }
            else{
                return response()->json(['error' => ['Gagal']], 200);
            }
        }
    }
    public function penjualanBeliSekarang_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id_barang' => 'required',
            'id_alamat' => 'required',
            'jasa_pengiriman' => 'required',
            'ongkir' => 'required',
            'potongan' => 'required',
            'id_voucher' => 'required',
            'total_harga' => 'required',
            'jumlah_barang' => 'required',
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $detailBarang = Barang::select('*')->where('id_barang', $request['id_barang'])->get();
        foreach($detailBarang as $db){
            $barang = $db['nama_barang'];
            $stok_barang = $db['stok_barang'];
        }
        if($stok_barang <= 0){
            return response()->json(['error' => ['Stok Barang Tidak Mencukupi']], 200);
        }
        else{
            date_default_timezone_set("Asia/Jakarta");
            $id_penjualan = 'KP'.date('Ymdhis').rand(0, 100);
            $params = ([ 
                'external_id' => $id_penjualan,
                'payer_email' => $request['email'],
                'description' => $request['email'] .' order '. $barang,
                'amount' => $request['total_harga']
            ]);
            $createInvoice = \Xendit\Invoice::create($params);
            $date = date('Y-m-d h:i:s');
            if($createInvoice){
                $kurang_stok_barang = Barang::where('id_barang', $request['id_barang'])->update(['stok_barang' => $stok_barang - $request['jumlah_barang']]);
                date_default_timezone_set("Asia/Jakarta");
                $penjualan = Penjualan::create([
                    'id_penjualan' => $id_penjualan,
                    'id_barang' => $request['id_barang'],
                    'id_alamat' => $request['id_alamat'],
                    'tanggal_penjualan' => $date,
                    'harga_barang' => $request['harga_barang'],
                    'jumlah_barang' => $request['jumlah_barang'],
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
                $log_penjualan = LogPenjualan::create([
                    'id_penjualan' => $id_penjualan,
                    'status_log' => 'Pesanan dibuat',
                    'tanggal_penjualan_log' => $date,
                ]);
                $response['invoice'] = $createInvoice;
                $response['penjualan'] = $penjualan;
                $response['detail_barang'] = $barang;
                $response['log_penjualan'] = $log_penjualan;
                return response()->json($response, 200);
            }
            else{
                return response()->json(['error' => ['Gagal']], 200);
            }
        }
    }
    public function penjualanBelumDibayar_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang',
        'pembayaran.id_pembayaran', 'pembayaran.link_invoice', 
        'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.total_harga',
        'penjualan.status', 'penjualan.tanggal_penjualan',
        'log_penjualan.status_log', 'log_penjualan.tanggal_penjualan_log'
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('log_penjualan', 'penjualan.id_penjualan', '=', 'log_penjualan.id_penjualan')
        ->where('penjualan.status', 'PENDING')
        ->where('penjualan.email', $request['email'])
        ->orderBy('penjualan.tanggal_penjualan', 'DESC')
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            $temp['id_penjualan'] = $key;
            foreach($item as $b){
                $total_harga = $b['total_harga'];
                $link_invoie = $b['link_invoice'];
                $id_pembayaran = $b['id_pembayaran'];
                $status_penjualan = $b['status'];
                $status_log = $b['status_log'];
                $tanggal_penjualan = $b['tanggal_penjualan'];
            }
            // $getInvoice = \Xendit\Invoice::retrieve($id_pembayaran);
            // if($getInvoice['status'] != $status_penjualan){
            //     $status = $getInvoice['status'];
            //     Penjualan::where('id_penjualan', $key)->update(['status' => $status]);
            // }
            // if($getInvoice['status'] == 'EXPIRED'){
            //     LogPenjualan::create([
            //         'id_penjualan' => $key,
            //         'status_log' => 'Pesanan melewati batas waktu pembayaran',
            //         'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
            //     ]);
            // }
            // if($getInvoice['status'] == 'SETTLED'){
            //     LogPenjualan::create([
            //         'id_penjualan' => $key,
            //         'status' => 'Pembayaran berhasil',
            //         'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
            //     ]);
            // }
            $temp['status'] = $status_penjualan;
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['total_harga'] = $total_harga;
            $temp['link_invoice'] = $link_invoie;
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['tanggal_penjualan']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Belum Dibayar Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }
    public function SemuaPenjualan_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.email', $request['email'])
        ->orderBy('penjualan.tanggal_penjualan', 'DESC')
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
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['total_harga'] = $total_harga;
            $temp['link_invoice'] = $link_invoie;
            $temp['status'] = $status_db;
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }
    public function badgePenjualan_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('penjualan.id_penjualan','penjualan.status')
        ->where('penjualan.email', $request['email'])
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            $temp['id_penjualan'] = $key;
            foreach($item as $b){
                $status_db = $b['status'];
            }
            $temp['status'] = $status_db;
            $barang[] = $temp;
        });;
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }
    public function penjualanDibayar_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang',
        'pembayaran.id_pembayaran', 'pembayaran.link_invoice',
        'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 
        'penjualan.status', 'penjualan.tanggal_penjualan',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'SETTLED')
        ->where('penjualan.email', $request['email'])
        ->orderBy('penjualan.tanggal_penjualan', 'DESC')
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            $temp['id_penjualan'] = $key;
            foreach($item as $b){
                $total_harga = $b['total_harga'];
                $link_invoie = $b['link_invoice'];
                $id_pembayaran = $b['id_pembayaran'];
                $status_penjualan = $b['status'];
                $tanggal_penjualan = $b['tanggal_penjualan'];
            }
        
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['status'] = $status_penjualan;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['total_harga'] = $total_harga;
            $temp['link_invoice'] = $link_invoie;
            
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                return $i;
            })->all();
            $barang[] = $temp;
        });
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Dibayar Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }
    public function penjualanDalamProses_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'PROCESSED')
        ->where('penjualan.email', $request['email'])
        ->orderBy('penjualan.tanggal_penjualan', 'DESC')
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
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['status'] = $status_db;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['total_harga'] = $total_harga;
            $temp['link_invoice'] = $link_invoie;
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Dalam Proses Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }    
    public function penjualanSedangDikirim_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'DELIVERY')
        ->where('penjualan.email', $request['email'])
        ->orderBy('penjualan.tanggal_penjualan', 'DESC')
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
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['status'] = $status_db;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['total_harga'] = $total_harga;
            $temp['link_invoice'] = $link_invoie;
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Sedang Dikirim Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }
    public function penjualanDikomplain_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'COMPLAINT')
        ->where('penjualan.email', $request['email'])
        ->orderBy('penjualan.tanggal_penjualan', 'DESC')
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
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['status'] = $status_db;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['total_harga'] = $total_harga;
            $temp['link_invoice'] = $link_invoie;
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Dikomplain Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }
    public function penjualanTerkirim_user(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'DELIVERED')
        ->where('penjualan.email', $request['email'])
        ->orderBy('penjualan.tanggal_penjualan', 'DESC')
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
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['status'] = $status_db;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['total_harga'] = $total_harga;
            $temp['link_invoice'] = $link_invoie;
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Sedang Dikirim Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }
    
    public function detailPenjualanSedangDikirim_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal', 'barang.id_barang',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.nomor_resi',
         'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'DELIVERY')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
                $nomor_resi = $b['nomor_resi'];
            }
            $kode_voucher = Voucher::select('kode')->orWhere('id_voucher', $id_voucher)->get();
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['kode_voucher'] = $kode_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['nomor_resi'] = $nomor_resi;
            $temp['log_penjualan'] = LogPenjualan::where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                unset($i['nomor_resi']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    public function detailPenjualanTidakTerbayar_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal', 'barang.id_barang',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.nomor_resi',
         'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'EXPIRED')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
                $nomor_resi = $b['nomor_resi'];
            }
            $kode_voucher = Voucher::select('kode')->orWhere('id_voucher', $id_voucher)->get();
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['kode_voucher'] = $kode_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['nomor_resi'] = $nomor_resi;
            $temp['log_penjualan'] = LogPenjualan::where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                unset($i['nomor_resi']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    public function detailPenjualanTerkirim_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal', 'barang.id_barang',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.nomor_resi',
         'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'DELIVERED')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
                $nomor_resi = $b['nomor_resi'];
            }
            $kode_voucher = Voucher::select('kode')->orWhere('id_voucher', $id_voucher)->get();
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['kode_voucher'] = $kode_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['nomor_resi'] = $nomor_resi;
            $temp['log_penjualan'] = LogPenjualan::where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                unset($i['nomor_resi']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    public function detailPenjualanDikomplain_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal', 'barang.id_barang',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.nomor_resi',
         'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'COMPLAINT')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
                $nomor_resi = $b['nomor_resi'];
            }
            $kode_voucher = Voucher::select('kode')->orWhere('id_voucher', $id_voucher)->get();
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['kode_voucher'] = $kode_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['nomor_resi'] = $nomor_resi;
            $temp['log_penjualan'] = LogPenjualan::where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                unset($i['nomor_resi']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    public function detailPenjualanBelumDibayar_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal', 'barang.id_barang',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'PENDING')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
            }
            $kode_voucher = Voucher::select('kode')->orWhere('id_voucher', $id_voucher)->get();
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['kode_voucher'] = $kode_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['log_penjualan'] = LogPenjualan::where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    
    public function detailPenjualanDibayar_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal', 'barang.id_barang',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'SETTLED')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
            }
            $kode_voucher = Voucher::select('kode')->orWhere('id_voucher', $id_voucher)->get();
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['kode_voucher'] = $kode_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['log_penjualan'] = LogPenjualan::where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;
        return response()->json($response, 200);
    }
    public function detailPenjualanDalamProses_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal', 'barang.id_barang',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'PROCESSED')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
            }
            $kode_voucher = Voucher::select('kode')->orWhere('id_voucher', $id_voucher)->get();
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['kode_voucher'] = $kode_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['log_penjualan'] = LogPenjualan::where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    public function refreshUser(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.id_barang',
        'pembayaran.id_pembayaran', 
        'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.id_voucher',
        'penjualan.status', 'penjualan.tanggal_penjualan'
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'PENDING')
        ->where('penjualan.email', $request['email'])
        ->orderBy('penjualan.tanggal_penjualan', 'DESC')
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            $temp['id_penjualan'] = $key;
            foreach($item as $b){
                $id_pembayaran = $b['id_pembayaran'];
                $status_penjualan = $b['status'];
                $status_log = $b['status_log'];
                $tanggal_penjualan = $b['tanggal_penjualan'];
                $id_barang = $b['id_barang'];
                $jumlah_barang = $b['jumlah_barang'];
                $id_voucher = $b['id_voucher'];
            }
            $getInvoice = \Xendit\Invoice::retrieve($id_pembayaran);
            if($getInvoice['status'] != $status_penjualan){
                $status = $getInvoice['status'];
                Penjualan::where('id_penjualan', $key)->update(['status' => $status]);
            }
            if($getInvoice['status'] == 'EXPIRED'){
                LogPenjualan::create([
                    'id_penjualan' => $key,
                    'status_log' => 'Pesanan melewati batas waktu pembayaran',
                    'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
                ]);
                
                $temp['barang'] = $item->transform(function ($i, $k) {
                    unset($i['id_penjualan']);
                    unset($i['total_harga']);
                    unset($i['link_invoice']);
                    unset($i['id_pembayaran']);
                    unset($i['status']);
                    unset($i['tanggal_penjualan']);
                    $stok_barang = Barang::select('stok_barang')->where('id_barang', $i['id_barang'])->get()[0]->stok_barang;
                    Barang::where('id_barang', $i['id_barang'])->update(['stok_barang' => $stok_barang + $i['jumlah_barang']]);
                    return $i;
                })->all();
            }
            if($getInvoice['status'] == 'SETTLED'){
                LogPenjualan::create([
                    'id_penjualan' => $key,
                    'status_log' => 'Pembayaran berhasil',
                    'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
                ]);
                if($id_voucher > 0){
                    $kuota = Voucher::select('kuota')->where('id_voucher', $id_voucher)->get()[0]->kuota;
                    Voucher::where('id_voucher', $id_voucher)->update(['kuota' => $kuota + 1]);
                }
            }
            $temp['status'] = $status_penjualan;
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['xendit'] = $getInvoice;
            $barang[] = $temp;
        });
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Belum Dibayar Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }
    public function syncUser(Request $request){
        $barang = [];
        Penjualan::select('pembayaran.id_pembayaran', 'penjualan.id_penjualan', 'penjualan.status')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'PENDING')
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            $temp['id_penjualan'] = $key;
            foreach($item as $b){
                $status_db = $b['status'];
                $id_pembayaran = $b['id_pembayaran'];
            }
            $getInvoice = \Xendit\Invoice::retrieve($id_pembayaran);
            if($getInvoice['status'] != $status_db){
                $status = $getInvoice['status'];
                Penjualan::where('id_penjualan', $key)->update(['status' => $status]);
            }
            if($getInvoice['status'] == 'EXPIRED'){ 
                date_default_timezone_set("Asia/Jakarta");     
                LogPenjualan::create([
                    'id_penjualan' => $key,
                    'status_log' => 'Pesanan melewati batas waktu pembayaran',
                    'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
                ]);
            }
            if($getInvoice['status'] == 'SETTLED'){
                date_default_timezone_set("Asia/Jakarta");
                LogPenjualan::create([
                    'id_penjualan' => $key,
                    'status_log' => 'Pembayaran berhasil',
                    'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
                ]);
            }
            $temp['status'] = $status_db;
            $temp['id_pembayaran'] = $id_pembayaran;
            $barang[] = $temp;
        });
        return response()->json(['success' => ['Successfully Sync']], 200);
    }
    public function prosesPenjualanTerkirim_user(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal', 'barang.id_barang',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.nomor_resi',
         'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'DELIVERY')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            $temp['id_penjualan'] = $key;
            foreach($item as $b){
                $total_harga = $b['total_harga'];
                $link_invoie = $b['link_invoice'];
                $id_pembayaran = $b['id_pembayaran'];
                $id_barang = $b['id_barang']; 
                $status_db = $b['status'];
                $tanggal_penjualan = $b['tanggal_penjualan'];
                $email = $b['email'];
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
                $nomor_resi = $b['nomor_resi'];
                $jumlah_barang = $b['jumlah_barang'];
            }
            Penjualan::where('id_penjualan', $key)->update(['status' => 'DELIVERED']);
            LogPenjualan::create([
                'id_penjualan' => $key,
                'status_log' => 'Pesanan Terkirim',
                'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
            ]);
            $kode_voucher = Voucher::select('kode')->orWhere('id_voucher', $id_voucher)->get();
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['kode_voucher'] = $kode_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['nomor_resi'] = $nomor_resi;
            $temp['log_penjualan'] = LogPenjualan::where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                unset($i['nomor_resi']);
                $q_barang = Barang::where('id_barang', $i['id_barang'])->get();
                foreach($q_barang as $qb){
                    $terjual = $qb['terjual'];
                }
                Barang::where('id_barang', $i['id_barang'])->update(['terjual' => $terjual+$i['jumlah_barang']]);
                return $i;
            })->all();
            $barang[] = $temp;  
        });;
        if(count($barang) <= 0){
            return response()->json(['error' => ['Pesanan Terikirim Kosong']], 200);
        }
        else{
            $response['barang'] = $barang;
        return response()->json($response, 200);
        }
    }
    // ADMIN
    public function penjualanDibayar_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'SETTLED')
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
    public function detailPenjualanDibayar_admin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kecamatan','alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'SETTLED')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kecamatan = $b['kecamatan'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kecamatan'] = $kecamatan;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kecamatan']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;
        return response()->json($response, 200);
    }
    public function prosesPenjualanDibayar_admin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $proses_penjualan = Penjualan::where('id_penjualan', $request['id_penjualan'])->update(['status' => 'PROCESSED']);
        $response = $proses_penjualan;
        if($proses_penjualan){
            LogPenjualan::create([
                'id_penjualan' => $request['id_penjualan'],
                'status_log' => 'Pesanan diproses',
                'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
            ]);
            return response()->json(['success' => ['Berhasil Diproses']], 200);
        }
    }
    public function penjualanDiproses_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'PROCESSED')
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
    public function cariPenjualanDiproses_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'PROCESSED')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
    public function detailPenjualanDiproses_admin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'PROCESSED')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
                $nomor_resi = $b['nomor_resi'];
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['nomor_resi'] = $nomor_resi;
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                unset($i['nomor_resi']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    public function prosesPenjualanDiproses_admin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
            'nomor_resi' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $proses_penjualan = Penjualan::where('id_penjualan', $request['id_penjualan'])->update(['status' => 'DELIVERY', 'nomor_resi' => $request['nomor_resi']]);
        $response = $proses_penjualan;
        if($proses_penjualan){
            LogPenjualan::create([
                'id_penjualan' => $request['id_penjualan'],
                'status_log' => 'Pesanan dikirim',
                'tanggal_penjualan_log' => date('Y-m-d h:i:s'),
            ]);
            return response()->json(['success' => ['Berhasil Diproses']], 200);
        }
    }
    public function penjualanDikirim_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'DELIVERY')
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
    public function penjualanDikomplain_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'COMPLAINT')
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
    public function detailPenjualanDikirim_admin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan', 'penjualan.nomor_resi',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'DELIVERY')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
                $nomor_resi = $b['nomor_resi'];
            }
            
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['nomor_resi'] = $nomor_resi;
            $temp['imei'] = Imei::select('imei')->where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                unset($i['nomor_resi']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;
        return response()->json($response, 200);
    }
    public function penjualanTerkirim_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'DELIVERED')
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
    public function detailPenjualanTerkirim_admin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan', 'penjualan.nomor_resi',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'DELIVERED')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
                $nomor_resi = $b['nomor_resi'];
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['nomor_resi'] = $nomor_resi;
            $temp['imei'] = Imei::select('imei')->where('id_penjualan', $key)->get();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                unset($i['nomor_resi']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;
        return response()->json($response, 200);
    }
    public function detailPenjualanDikomplain_admin(Request $request){
        $validator = Validator::make($request->all(), [
            'id_penjualan' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->all());
        };
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang', 'barang.warna', 'barang.ram', 'barang.internal',
         'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email', 'penjualan.jasa_pengiriman', 'penjualan.harga_barang', 'penjualan.ongkir', 'penjualan.harga_barang', 'penjualan.id_voucher', 'penjualan.potongan', 'penjualan.nomor_resi',
         'alamat.nomor_telepon', 'alamat.nama_penerima', 'alamat.id_alamat', 'alamat.alamat', 'alamat.provinsi', 'alamat.kabupaten', 'alamat.kode_pos',
         'pembayaran.link_invoice' ,'pembayaran.id_pembayaran',
        )
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->join('alamat', 'penjualan.id_alamat', '=', 'alamat.id_alamat')
        ->where('penjualan.status', 'COMPLAINT')
        ->where('penjualan.id_penjualan', $request['id_penjualan'])
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
                $id_alamat = $b['id_alamat'];
                $nomor_telepon = $b['nomor_telepon'];
                $nama_penerima = $b['nama_penerima'];
                $alamat = $b['alamat'];
                $provinsi = $b['provinsi'];
                $kabupaten = $b['kabupaten'];
                $kode_pos = $b['kode_pos'];
                $jasa_pengiriman = $b['jasa_pengiriman'];
                $ongkir = $b['ongkir'];
                $id_voucher = $b['id_voucher'];
                $potongan = $b['potongan'];
                $nomor_resi = $b['nomor_resi'];
            }
            $temp['tanggal_penjualan'] = $tanggal_penjualan;
            $temp['email'] = $email;
            $temp['id_pembayaran'] = $id_pembayaran;
            $temp['status'] = $status_db;
            $temp['total_harga'] = $total_harga;
            $temp['id_voucher'] = $id_voucher;
            $temp['potongan'] = $potongan;
            $temp['link_invoice'] = $link_invoie;
            $temp['id_alamat'] = $id_alamat;
            $temp['nama_penerima'] = $nama_penerima;
            $temp['nomor_telepon'] = $nomor_telepon;
            $temp['alamat'] = $alamat;
            $temp['provinsi'] = $provinsi;
            $temp['kabupaten'] = $kabupaten;
            $temp['kode_pos'] = $kode_pos;
            $temp['jasa_pengiriman'] = $jasa_pengiriman;
            $temp['ongkir'] = $ongkir;
            $temp['nomor_resi'] = $nomor_resi;
            $temp['imei'] = Imei::select('imei')->where('id_penjualan', $key)->get();
            $temp['komplain'] = Komplain::where('id_penjualan', $key)->orderBy('tanggal_komplain', 'desc')->first();
            $temp['barang'] = $item->transform(function ($i, $k) {
                unset($i['tanggal_penjualan']);
                unset($i['id_penjualan']);
                unset($i['total_harga']);
                unset($i['link_invoice']);
                unset($i['id_pembayaran']);
                unset($i['status']);
                unset($i['email']);
                unset($i['id_alamat']);
                unset($i['nomor_telepon']);
                unset($i['nama_penerima']);
                unset($i['alamat']);
                unset($i['provinsi']);
                unset($i['kabupaten']);
                unset($i['kode_pos']);
                unset($i['jasa_pengiriman']);
                unset($i['ongkir']);
                unset($i['id_voucher']);
                unset($i['potongan']);
                unset($i['nomor_resi']);
                return $i;
            })->all();
            $barang[] = $temp;
        });;
        $response['barang'] = $barang;
        return response()->json($response, 200);
    }
    public function totalPenjualan_admin(Request $request){
        $barang = [];
        Penjualan::select('penjualan.total_harga', 'penjualan.id_penjualan')
        ->select(DB::raw("SUM(penjualan.total_harga) as total_penjualan"))
        ->where('penjualan.status', 'DELIVERED')
        ->whereMonth('penjualan.tanggal_penjualan', '=', $request['bulan'])
        ->whereYear('penjualan.tanggal_penjualan', $request['tahun'])
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            foreach($item as $b){
                $total_penjualan = $b['total_penjualan'];
            }
            $temp['total_penjualan'] = $total_penjualan;
            $barang[] = $temp;
        });

        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    public function totalPenjualanMasuk_admin(Request $request){
        $barang = [];
        Penjualan::select('penjualan.total_harga', 'penjualan.id_penjualan')
        ->select(DB::raw("SUM(penjualan.total_harga) as total_penjualan"))
        ->where('penjualan.status', '!=', 'DELIVERED')
        ->where('penjualan.status', '!=', 'EXPIRED')
        ->whereMonth('penjualan.tanggal_penjualan', '=', $request['bulan'])
        ->whereYear('penjualan.tanggal_penjualan', $request['tahun'])
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            foreach($item as $b){
                $total_penjualan = $b['total_penjualan'];
            }
            $temp['total_penjualan'] = $total_penjualan;
            $barang[] = $temp;
        });

        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    public function totalOngkirPenjualan_admin(Request $request){
        $barang = [];
        Penjualan::select('penjualan.ongkir', 'penjualan.id_penjualan')
        ->select(DB::raw("SUM(penjualan.ongkir) as total_ongkir"))
        ->where('penjualan.status', '!=', 'EXPIRED')
        ->whereMonth('penjualan.tanggal_penjualan', '=', $request['bulan'])
        ->whereYear('penjualan.tanggal_penjualan', $request['tahun'])
        ->get()
        ->groupBy('id_penjualan')
        ->each(function ($item, $key) use (&$barang) {
            $temp = [];
            foreach($item as $b){
                $total_ongkir = $b['total_ongkir'];
            }
            $temp['total_ongkir'] = $total_ongkir;
            $barang[] = $temp;
        });

        $response['barang'] = $barang;

        return response()->json($response, 200);
    }
    public function totalPenjualanDibayar_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 
        'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 
        'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan', 
        'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'SETTLED')
        ->whereMonth('penjualan.tanggal_penjualan', '=', $request['bulan'])
        ->whereYear('penjualan.tanggal_penjualan', $request['tahun'])
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
        $response['barang'] = count($barang);

        return response()->json($response, 200);
    }
    public function totalPenjualanDiproses_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran',
        'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 
        'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan',
        'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'PROCESSED')
        ->whereMonth('penjualan.tanggal_penjualan', '=', $request['bulan'])
        ->whereYear('penjualan.tanggal_penjualan', $request['tahun'])
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
        $response['barang'] = count($barang);

        return response()->json($response, 200);
    }
    public function totalPenjualanDikirim_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran',
        'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 
        'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'DELIVERY')
        ->whereMonth('penjualan.tanggal_penjualan', '=', $request['bulan'])
        ->whereYear('penjualan.tanggal_penjualan', $request['tahun'])
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
        $response['barang'] = count($barang);

        return response()->json($response, 200);
    }
    public function totalPenjualanTerkirim_admin(Request $request){
        $barang = [];
        Penjualan::select('barang.gambar','barang.nama_barang','pembayaran.id_pembayaran', 'penjualan.total_harga', 'penjualan.id_penjualan', 'penjualan.jumlah_barang', 'pembayaran.link_invoice', 'penjualan.status', 'penjualan.tanggal_penjualan', 'penjualan.email')
        ->join('barang', 'barang.id_barang', '=', 'penjualan.id_barang')
        ->join('pembayaran', 'penjualan.id_penjualan', '=', 'pembayaran.id_penjualan')
        ->where('penjualan.status', 'DELIVERED')
        ->whereMonth('penjualan.tanggal_penjualan', '=', $request['bulan'])
        ->whereYear('penjualan.tanggal_penjualan', $request['tahun'])
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
        });
        $response['barang'] = count($barang);

        return response()->json($response, 200);
    }

}
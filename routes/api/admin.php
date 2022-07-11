<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PenjualanOfflineController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ImeiController;
use App\Http\Controllers\KomplainController;

/*

|--------------------------------------------------------------------------

| API Routes

|--------------------------------------------------------------------------

|

| Here is where you can register API routes for your application. These

| routes are loaded by the RouteServiceProvider within a group which

| is assigned the "api" middleware group. Enjoy building your API!

|

*/


Route::post('admin/login',[AuthController::class, 'adminLogin'])->name('adminLogin');
Route::post('admin/signup',[AuthController::class, 'adminSignup'])->name('adminSignup');
Route::group( ['prefix' => 'admin','middleware' => ['auth:admin-api','scopes:admin'] ],function(){
   // authenticated staff routes here 
    Route::get('profile',[AuthController::class, 'adminProfile']);
    Route::get('logout',[AuthController::class, 'logOut']);
    Route::get('barang',[BarangController::class, 'barang'])->name('barang');
    Route::get('pembelian',[PembelianController::class, 'pembelian'])->name('pembelian');
});
Route::post('admin/tambah_barang',[BarangController::class, 'tambahBarang'])->name('tambahBarang');
Route::post('admin/detail_barang',[BarangController::class, 'detailBarang_admin'])->name('detailBarang_admin');
Route::post('admin/tambah_pembelian',[PembelianController::class, 'tambahPembelian'])->name('tambahPembelian');
Route::post('admin/tambah_voucher',[VoucherController::class, 'tambahVoucher'])->name('tambahVoucher');
Route::get('admin/voucher',[VoucherController::class, 'VoucherAll'])->name('VoucherAll');
Route::get('admin/detail_penggunaan_voucher',[VoucherController::class, 'detailPenggunaanVoucher'])->name('detailPenggunaanVoucher');
Route::post('admin/cek_kesamaan_barang',[BarangController::class, 'cekKesamaanBarang'])->name('cekKesamaanBarang');
Route::post('admin/cek_nama_barang',[BarangController::class, 'cekNamaBarang'])->name('cekNamaBarang');
Route::post('admin/cek_barang',[BarangController::class, 'cekBarang'])->name('cekBarang');
Route::post('admin/cek_ram_barang',[BarangController::class, 'cekRamBarang'])->name('cekRamBarang');
Route::post('admin/cek_internal_barang',[BarangController::class, 'cekInternalBarang'])->name('cekInternalBarang');
Route::post('admin/cek_warna_barang',[BarangController::class, 'cekWarnaBarang'])->name('cekWarnaBarang');
Route::get('admin/penjualan_dibayar',[PenjualanController::class, 'penjualanDibayar_admin'])->name('penjualanDibayar_admin');
Route::get('admin/detail_penjualan_dibayar',[PenjualanController::class, 'detailPenjualanDibayar_admin'])->name('detailPenjualanDibayar_admin');
Route::get('admin/proses_penjualan_dibayar',[PenjualanController::class, 'prosesPenjualanDibayar_admin'])->name('prosesPenjualanDibayar_admin');
Route::get('admin/penjualan_diproses',[PenjualanController::class, 'penjualanDiproses_admin'])->name('penjualanDiproses_admin');
Route::get('admin/cari_penjualan_diproses',[PenjualanController::class, 'cariPenjualanDiproses_admin'])->name('cariPenjualanDiproses_admin');
Route::get('admin/detail_penjualan_diproses',[PenjualanController::class, 'detailPenjualanDiproses_admin'])->name('detailPenjualanDiproses_admin');
Route::post('admin/proses_penjualan_diproses',[PenjualanController::class, 'prosesPenjualanDiproses_admin'])->name('prosesPenjualanDiproses_admin');
Route::get('admin/penjualan_dikirim',[PenjualanController::class, 'penjualanDikirim_admin'])->name('penjualanDikirim_admin');
Route::get('admin/detail_penjualan_dikirim',[PenjualanController::class, 'detailPenjualanDikirim_admin'])->name('detailPenjualanDikirim_admin');
Route::get('admin/penjualan_terkirim',[PenjualanController::class, 'penjualanTerkirim_admin'])->name('penjualanTerkirim_admin');
Route::get('admin/detail_penjualan_terkirim',[PenjualanController::class, 'detailPenjualanTerkirim_admin'])->name('detailPenjualanTerkirim_admin');
Route::get('admin/penjualan_dikomplain',[PenjualanController::class, 'penjualanDikomplain_admin'])->name('penjualanDikomplain_admin');
Route::get('admin/detail_penjualan_dikomplain',[PenjualanController::class, 'detailPenjualanDikomplain_admin'])->name('detailPenjualanDikomplain_admin');
Route::post('admin/tambah_banner',[BannerController::class, 'tambahBanner'])->name('tambahBanner');
Route::get('admin/banner',[BannerController::class, 'getBanner'])->name('getBanner');
Route::get('admin/detail_banner',[BannerController::class, 'detailBanner'])->name('detailBanner');
Route::post('admin/total_ongkir_penjualan',[PenjualanController::class, 'totalOngkirPenjualan_admin'])->name('totalOngkirPenjualan_admin');
Route::post('admin/total_penjualan',[PenjualanController::class, 'totalPenjualan_admin'])->name('totalPenjualan_admin');
Route::post('admin/total_penjualan_masuk',[PenjualanController::class, 'totalPenjualanMasuk_admin'])->name('totalPenjualanMasuk_admin');
Route::post('admin/total_pembelian',[PembelianController::class, 'totalPembelian'])->name('totalPembelian');
Route::post('admin/total_penjualan_dibayar',[PenjualanController::class, 'totalPenjualanDibayar_admin'])->name('totalPenjualanDibayar_admin');
Route::post('admin/total_penjualan_diproses',[PenjualanController::class, 'totalPenjualanDiproses_admin'])->name('totalPenjualanDiproses_admin');
Route::post('admin/total_penjualan_dikirim',[PenjualanController::class, 'totalPenjualanDikirim_admin'])->name('totalPenjualanDikirim_admin');
Route::post('admin/total_penjualan_terkirim',[PenjualanController::class, 'totalPenjualanTerkirim_admin'])->name('totalPenjualanTerkirim_admin');


Route::post('admin/tambah_penjualan_offline',[PenjualanOfflineController::class, 'tambahPenjualanOffline_admin'])->name('tambahPenjualanOffline_admin');
Route::get('admin/penjualan_offline',[PenjualanOfflineController::class, 'penjualanOffline_admin'])->name('penjualanOffline_admin');
Route::post('admin/tambah_notifikasi',[NotifikasiController::class, 'tambahNotifikasi'])->name('tambahNotifikasi');
Route::get('admin/notifikasi',[NotifikasiController::class, 'notifikasi'])->name('notifikasi');
Route::post('admin/tambah_imei',[ImeiController::class, 'tambahImei'])->name('tambahImei');
Route::post('admin/update_notifikasi',[NotifikasiController::class, 'updateNotifikasi'])->name('updateNotifikasi');

Route::post('admin/komplain_tanggapan_admin',[KomplainController::class, 'tanggapanAdmin'])->name('tanggapanAdmin');
Route::get('admin/komplain_selesai',[KomplainController::class, 'komplainSelesai'])->name('komplainSelesai');


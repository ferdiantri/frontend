<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\BannerController;
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


Route::post('user/login',[AuthController::class, 'userLogin'])->name('userLogin');
Route::post('user/signup',[AuthController::class, 'userSignup'])->name('userSignup');
Route::group( ['prefix' => 'user','middleware' => ['auth:user-api','scopes:user'] ],function(){
    Route::get('profile',[AuthController::class, 'userProfile']);
    Route::get('logout',[AuthController::class, 'logout']);
    
});
Route::post('user/ubah_foto_profile',[AuthController::class, 'changeProfilePicture_user'])->name('changeProfilePicture_user');
Route::get('user/barang',[BarangController::class, 'barang'])->name('barang');
Route::get('user/cari_barang',[BarangController::class, 'cariBarang'])->name('cariBarang');
Route::get('user/detail_barang',[BarangController::class, 'detailBarang'])->name('detailBarang');
Route::post('user/tambah_keranjang',[KeranjangController::class, 'tambahKeranjang'])->name('tambahKeranjang');
Route::get('user/keranjang',[KeranjangController::class, 'keranjang'])->name('keranjang');
Route::post('user/delete_keranjang',[KeranjangController::class, 'deleteKeranjang'])->name('deleteKeranjang');
Route::post('user/tambah_alamat',[AlamatController::class, 'tambahAlamat'])->name('tambahAlamat');
Route::post('user/ubah_alamat',[AlamatController::class, 'ubahAlamat'])->name('ubahAlamat');
Route::get('user/alamat',[AlamatController::class, 'alamat'])->name('alamat');
Route::post('user/voucher',[VoucherController::class, 'voucher'])->name('voucher');
Route::post('user/tambah_penjualan',[PenjualanController::class, 'tambahPenjualan_user'])->name('tambahPenjualan_user');
Route::post('user/tambah_penjualan_beli_sekarang',[PenjualanController::class, 'penjualanBeliSekarang_user'])->name('penjualanBeliSekarang_user');
Route::get('user/penjualan',[PenjualanController::class, 'semuaPenjualan_user'])->name('semuaPenjualan_user');
Route::get('user/badge_penjualan',[PenjualanController::class, 'badgePenjualan_user'])->name('badgePenjualan_user');
Route::get('user/penjualan_belum_dibayar',[PenjualanController::class, 'penjualanBelumDibayar_user'])->name('penjualanBelumDibayar_user');
Route::get('user/penjualan_dibayar',[PenjualanController::class, 'penjualanDibayar_user'])->name('penjualanDibayar_user');
Route::get('user/penjualan_dalam_proses',[PenjualanController::class, 'penjualanDalamProses_user'])->name('penjualanDalamProses_user');
Route::get('user/penjualan_sedang_dikirim',[PenjualanController::class, 'penjualanSedangDikirim_user'])->name('penjualanSedangDikirim_user');
Route::get('user/penjualan_terkirim',[PenjualanController::class, 'penjualanTerkirim_user'])->name('penjualanTerkirim_user');
Route::get('user/penjualan_dikomplain',[PenjualanController::class, 'penjualanDikomplain_user'])->name('penjualanDikomplain_user');
Route::get('user/refresh',[PenjualanController::class, 'refreshUser'])->name('refreshUser');
Route::get('user/sync',[PenjualanController::class, 'syncUser'])->name('syncUser');
Route::get('user/detail_penjualan_dibayar',[PenjualanController::class, 'detailPenjualanDibayar_user'])->name('detailPenjualanDibayar_user');
Route::get('user/detail_penjualan_belum_dibayar',[PenjualanController::class, 'detailPenjualanBelumDibayar_user'])->name('detailPenjualanBelumDibayar_user');
Route::get('user/detail_penjualan_dalam_proses',[PenjualanController::class, 'detailPenjualanDalamProses_user'])->name('detailPenjualanDalamProses_user');
Route::get('user/detail_penjualan_sedang_dikirim',[PenjualanController::class, 'detailPenjualanSedangDikirim_user'])->name('detailPenjualanSedangDikirim_user');
Route::get('user/detail_penjualan_terkirim',[PenjualanController::class, 'detailPenjualanTerkirim_user'])->name('detailPenjualanTerkirim_user');
Route::get('user/detail_penjualan_tidak_terbayar',[PenjualanController::class, 'detailPenjualanTidakTerbayar_user'])->name('detailPenjualanTidakTerbayar_user');
Route::get('user/detail_penjualan_dikomplain',[PenjualanController::class, 'detailPenjualanDikomplain_user'])->name('detailPenjualanDikomplain_user');
Route::get('user/proses_penjualan_terkirim',[PenjualanController::class, 'prosesPenjualanTerkirim_user'])->name('prosesPenjualanTerkirim_user');
Route::get('user/banner',[BannerController::class, 'getBanner'])->name('getBanner');
Route::post('user/tambah_komplain',[KomplainController::class, 'tambahKomplain'])->name('tambahKomplain');
Route::get('user/komplain',[KomplainController::class, 'komplain'])->name('komplain');
Route::get('user/komplain_selesai',[KomplainController::class, 'komplainSelesai'])->name('komplainSelesai');
import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import SignUp from '../views/SignUp.vue'
import Dashboard from '../views/Dashboard.vue'
import TambahBarang from '../views/TambahBarang.vue'
import Stok from '../views/Stok.vue'
import TambahStok from '../views/TambahStok.vue'
import Barang from '../views/Barang.vue'
import TambahVoucher from '../views/TambahVoucher.vue'
import Voucher from '../views/Voucher.vue'
import PenjualanSudahDibayar from '../views/PenjualanSudahDibayar.vue'
import DetailPenjualanSudahDibayar from '../views/DetailPenjualanSudahDibayar.vue'
import PenjualanSedangDiproses from '../views/PenjualanSedangDiproses.vue'
import DetailPenjualanSedangDiproses from '../views/DetailPenjualanSedangDiproses.vue'
import PenjualanSedangDikirim from '../views/PenjualanSedangDikirim.vue'
import DetailPenjualanSedangDikirim from '../views/DetailPenjualanSedangDikirim.vue'
import TambahBanner from '../views/TambahBanner.vue'
import Banner from '../views/Banner.vue'
import DetailBanner from '../views/DetailBanner.vue'
import DetailBannerUser from '../views/DetailBannerUser.vue'
import PenjualanTerkirim from '../views/PenjualanTerkirim.vue'
import DetailPenjualanTerkirim from '../views/DetailPenjualanTerkirim.vue'
import DetailVoucher from '../views/DetailVoucher.vue'
import TambahPenjualanOffline from '../views/TambahPenjualanOffline.vue'
import PenjualanOffline from '../views/PenjualanOffline.vue'
import Komplain from '../views/Komplain.vue'
import DetailKomplain from '../views/DetailKomplain.vue'
const routes = [
  {
    path: '/',
    name: 'Login',
    component: Login
  },
  {
    path: '/tambah_barang',
    name: 'TambahBarang',
    component: TambahBarang
  },
  {
    path: '/signup',
    name: 'SignUp',
    component: SignUp
  },
  {
    path: '/stok',
    name: 'Stok',
    component: Stok
  },
  {
    path: '/tambah_stok',
    name: 'TambahStok',
    component: TambahStok
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/barang',
    name: 'Barang',
    component: Barang
  },
  {
    path: '/tambah_voucher',
    name: 'TambahVoucher',
    component: TambahVoucher
  },
  {
    path: '/tambah_banner',
    name: 'TambahBanner',
    component: TambahBanner
  },
  {
    path: '/banner',
    name: 'Banner',
    component: Banner
  },
  {
    path: '/detail_banner/:id_banner',
    name: 'DetailBanner',
    component: DetailBanner
  },
  {
    path: '/detail_banner_user/:id_banner',
    name: 'DetailBannerUser',
    component: DetailBannerUser
  },
  {
    path: '/voucher',
    name: 'Voucher',
    component: Voucher
  },
  {
    path: '/tambah_penjualan_offline',
    name: 'TambahPenjualanOffline',
    component: TambahPenjualanOffline
  },
  {
    path: '/penjualan_offline',
    name: 'PenjualanOffline',
    component: PenjualanOffline
  },
  {
    path: '/penjualan_sudah_dibayar',
    name: 'PenjualanSudahDibayar',
    component: PenjualanSudahDibayar
  },
  {
    path: '/detail_penjualan_sudah_dibayar/:id_penjualan',
    name: 'DetailPenjualanSudahDibayar',
    component: DetailPenjualanSudahDibayar
  },
  {
    path: '/penjualan_sedang_diproses',
    name: 'PenjualanSedangDiproses',
    component: PenjualanSedangDiproses
  },
  {
    path: '/detail_penjualan_sedang_diproses/:id_penjualan',
    name: 'DetailPenjualanSedangDiproses',
    component: DetailPenjualanSedangDiproses
  },
  {
    path: '/penjualan_sedang_dikirim',
    name: 'PenjualanSedangDikirim',
    component: PenjualanSedangDikirim
  },
  {
    path: '/detail_penjualan_sedang_dikirim/:id_penjualan',
    name: 'DetailPenjualanSedangDikirim',
    component: DetailPenjualanSedangDikirim
  },
  {
    path: '/penjualan_terkirim',
    name: 'PenjualanTerkirim',
    component: PenjualanTerkirim
  },
  {
    path: '/detail_penjualan_terkirim/:id_penjualan',
    name: 'DetailPenjualanTerkirim',
    component: DetailPenjualanTerkirim
  },
  {
    path: '/detail_voucher/:id_voucher',
    name: 'DetailVoucher',
    component: DetailVoucher
  },
  {
    path: '/komplain',
    name: 'Komplain',
    component: Komplain
  },
  {
    path: '/detail_komplain/:id_penjualan',
    name: 'DetailKomplain',
    component: DetailKomplain
  },
  {
    path: '/about',
    name: 'About',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router

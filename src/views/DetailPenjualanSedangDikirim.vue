<template>
<div id="wrapper">

        <!-- Sidebar -->
        <Sidebar />
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <Topbar />
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Detail Penjualan Sedang Dikirim</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Data Detail Penjualan Sedang Dikirim</h6>
                                </div>
                                    <div class="card-body">
                                        <div  v-for="item in detail_penjualan" :key="item.id_penjualan">
                                            <table class="table ">
                                                <tr>
                                                    <td width="50%"><h6 class="m-0 font-weight-bold text-primary">Data Pengiriman</h6></td>
                                                    <td width="50%"><h6 class="m-0 font-weight-bold text-primary">Data Barang</h6></td>
                                                </tr>
                                                <tr>
                                                    <td width="50%">
                                                        <table class="table-bordered">
                                                            <tr>
                                                                <td>Id Alamat</td>
                                                                <td>{{item.id_alamat}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Nama Penerima</td>
                                                                <td>{{item.nama_penerima}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Nomor Telepon</td>
                                                                <td>{{item.nomor_telepon}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Alamat</td>
                                                                <td>{{item.alamat}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Provinsi</td>
                                                                <td>{{item.provinsi}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Kabupaten</td>
                                                                <td>{{item.kabupaten}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Kode Pos</td>
                                                                <td>{{item.kode_pos}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Jasa Pengiriman</td>
                                                                <td>{{item.jasa_pengiriman}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Nomor Resi</td>
                                                                <td>{{item.nomor_resi}}</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td width="50%">
                                                        <table class="table-bordered">
                                                            <tr>
                                                                <td>Nama Barang</td>
                                                                <td>Warna Barang</td>
                                                                <td>Varian</td>
                                                                <td>Jumlah Barang</td>
                                                                <td>Harga Barang</td>
                                                            </tr>
                                                            <tr v-for="barang in item.barang" :key="barang.nama_barang">
                                                                <td>{{barang.nama_barang}}</td>
                                                                <td>{{barang.warna}}</td>
                                                                <td>{{barang.ram}}/{{barang.internal}} GB</td>
                                                                <td>{{barang.jumlah_barang}}</td>
                                                                <td>{{barang.harga_barang}}</td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <table>
                                                                        <tr>
                                                                            <td width="50%"><h6 class="m-0 font-weight-bold text-primary">Data Harga</h6></td>
                                                                        </tr>
                                                                    </table>
                                                                    <hr>
                                                                    <table class="table-bordered">
                                                                        <tr>
                                                                            <td>Id Voucher</td>
                                                                            <td>{{item.id_voucher}}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Potongan</td>
                                                                            <td>{{item.potongan}}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Ongkos Kirim</td>
                                                                            <td>{{item.ongkir}}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Total Harga</td>
                                                                            <td>{{item.total_harga}}</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                                <td colspan="6">
                                                                    <table>
                                                                        <tr>
                                                                            <td width="50%"><h6 class="m-0 font-weight-bold text-primary">Data IMEI</h6></td>
                                                                        </tr>
                                                                    </table>
                                                                    <hr>
                                                                    <table class="table-bordered">
                                                                        <tr v-for="imei in item.imei" :key="imei">
                                                                            <td>IMEI</td>
                                                                            <td>{{imei.imei}}</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        
                                                    </td> 
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->
            <!-- Footer -->
            <Footer />
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->

    </div>

  
</template>
<script>
import { onMounted, ref } from 'vue'
import Footer from '@/components/Footer.vue';
import Sidebar from '@/components/Sidebar.vue';
import Topbar from '@/components/Topbar.vue';
import axios from 'axios';
export default {
  name: 'App',
  components: {
    Footer,
    Sidebar,
    Topbar,
  },
  setup() {
      const detail_penjualan = ref([])
      const detail_barang = ref([])
        onMounted(() =>{
            let path = window.location.pathname;
            let segments = path.split("/");
            console.log(segments[2]);
            axios.get(`http://192.168.43.22:8000/api/admin/detail_penjualan_dikirim?id_penjualan=${segments[2]}`).then(response =>{
                detail_penjualan.value = response.data.barang
                detail_barang.value = response.data.barang.map(item => item.barang)
                console.log(response.data.barang.map(item => item.barang))
            })
        })
        return {
                detail_penjualan,
                detail_barang
            }
    },
    methods: {
        async proses_pesanan() {
            try {
                let path = window.location.pathname;
                let segments = path.split("/");
                await axios
                axios.get(`http://192.168.43.22:8000/api/admin/proses_penjualan?id_penjualan=${segments[2]}`).then(response => {
                    if(response.data.success){
                        this.$router.push({
                        name: "Penjualan",
                        })
                    }
                })
            }
            catch(err){
                console.log(err);
            }
        }
    }
}
</script>
<style>

</style>

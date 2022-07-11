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
                        <h1 class="h3 mb-0 text-gray-800">Riwayat Tambah Stok</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Data Stok</h6>
                                </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <tr>
                                                <td width="17%">Id Barang</td>
                                                <td width="13%">Tanggal Pembelian</td>
                                                <td width="15%">Nama Barang</td>
                                                <td width="7%">RAM</td>
                                                <td width="10%">Internal</td>
                                                <td width="10%">Warna</td>
                                                <td width="7%">Jumlah Barang</td>
                                                <td width="10%">Harga Beli</td>
                                                <td width="10%">Total Harga</td>
                                            </tr>
                                        </table>
                                        <table class="table">
                                            <tr v-for="item in stok" :key="item.id_pembelian">
                                                <td width="17%">{{item.id_barang}}</td>
                                                <td width="13%">{{item.tanggal_pembelian}}</td>
                                                <td width="15%">{{item.nama_barang}}</td>
                                                <td width="7%">{{item.ram}} GB</td>
                                                <td width="10%">{{item.internal}} GB</td>
                                                <td width="10%">{{item.warna}}</td>
                                                <td width="7%">{{item.jumlah_barang}}</td>
                                                <td width="10%">{{item.harga_beli}}</td>
                                                <td width="10%">{{item.total_harga}}</td>
                                            </tr>
                                        </table>
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
      const stok = ref([])
        onMounted(() =>{
            axios.get('http://192.168.43.22:8000/api/admin/pembelian').then(response =>{
                console.log(response.data)
                stok.value = response.data
            })
        })
        return {
                stok
            }
    }
}
</script>
<style>

</style>

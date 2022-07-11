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
                        <h1 class="h3 mb-0 text-gray-800">Voucher</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Data Voucher</h6>
                                </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <tr>
                                                <td width="19%" align="center">Id Voucher</td>
                                                <td width="14%" align="center">Kode</td>
                                                <td width="17%" align="center">Minimal Pembelian</td>
                                                <td width="10%" align="center">Potongan</td>
                                                <td width="7%" align="center">Kuota</td>
                                                <td width="17%" align="center">Tanggal Mulai</td>
                                                <td width="17%" align="center">Tanggal Selesai</td>
                                                <td width="10%" align="center">Operasi</td>
                                            </tr>
                                        </table>
                                        <table class="table">
                                            <tr v-for="item in voucher" :key="item.id_voucher">
                                                <td width="19%">{{item.id_voucher}}</td>
                                                <td width="14%">{{item.kode}}</td>
                                                <td width="17%">{{item.min_pembelian}}</td>
                                                <td width="10%">{{item.potongan}}</td>
                                                <td width="7%">{{item.kuota}}</td>
                                                <td width="17%">{{item.tanggal_mulai}}</td>
                                                <td width="17%">{{item.tanggal_selesai}}</td>
                                                <td width="10%"><router-link :to="{name: 'DetailVoucher', params: { id_voucher: item.id_voucher}}">Detail</router-link></td>
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
      const voucher = ref([])
        onMounted(() =>{
            axios.get('http://192.168.43.22:8000/api/admin/voucher').then(response =>{
                console.log(response.data)
                voucher.value = response.data
            })
        })
        return {
                voucher
            }
    }
}
</script>
<style>

</style>

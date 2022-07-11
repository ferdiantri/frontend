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
                        <h1 class="h3 mb-0 text-gray-800">Barang</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Data Barang</h6>
                                </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <tr>
                                                <td width="14%" align="center">Gambar</td>
                                                <td width="17%" align="center">Id Barang</td>
                                                <td width="17%" align="center">Nama Barang</td>
                                                <td width="7%" align="center">RAM</td>
                                                <td width="10%" align="center">Internal</td>
                                                <td width="10%" align="center">Warna</td>
                                                <td width="10%" align="center">Stok</td>
                                                <!-- <td width="10%" align="center">Status</td> -->
                                                <td width="10%" align="center">Operasi</td>
                                            </tr>
                                        </table>
                                        <table class="table">
                                            <tr v-for="item in barang" :key="item.id_barang">
                                                
                                                <td width="14%" align="center"><img :src="'http://192.168.43.22:8000/'+item.gambar" width="100" height="100"></td>
                                                <td width="17%" align="center">{{item.id_barang}}</td>
                                                <td width="17%" align="center">{{item.nama_barang}}</td>
                                                <td width="7%" align="center">{{item.ram}} GB</td>
                                                <td width="10%" align="center">{{item.internal}} GB</td>
                                                <td width="10%" align="center">{{item.warna}}</td>
                                                <td width="10%" align="center" style="color:red" v-if="item.stok_barang < 10" :click="onAlert(item.id_barang)" >{{item.stok_barang}}</td>
                                                <td width="10%" align="center" v-else-if="item.stok_barang >= 5">{{item.stok_barang}}</td>
                                                <!-- <td width="10%" align="center">{{item.status}}</td> -->
                                                <td width="10%" align="center"><router-link :to="'/detail_barang/'+item.id_barang">Detail Barang</router-link></td>
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
      const barang = ref([])
        onMounted(() =>{
            const token = localStorage.getItem('token')
            const headers = {
                'Authorization': 'Bearer '+token
            }
            axios.get('http://192.168.43.22:8000/api/admin/barang', headers).then(response =>{
                console.log(response.data)
                barang.value = response.data
            })
        })
        return {
            barang
        }
    },
    methods: {
        async onAlert(id_barang) {
            const data = {
                id_barang : id_barang
            }
            axios.post('http://192.168.43.22:8000/api/admin/tambah_notifikasi', data).then(response =>{
                console.log(response.data)
            })
        }
    }
}
</script>
<style>

</style>

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
                        <h1 class="h3 mb-0 text-gray-800">Penjualan Sedang Dikirim</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Data Penjualan</h6>
                                </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <tr>
                                                <td width="18%">Id Penjualan</td>
                                                <td width="18%">Tanggal Penjualan</td>
                                                <td width="18%">Total Harga</td>
                                                <!-- <td width="18%">Link Invoice</td> -->
                                                <td width="18%">Email</td>
                                                <td width="18%">Status</td>
                                                <td width="10%"></td>
                                            </tr>
                                        </table>
                                        <table class="table">
                                            <tr v-for="item in penjualan_sedang_dikirim" :key="item.id_penjualan">
                                                <td width="18%">{{item.id_penjualan}}</td>
                                                <td width="18%">{{item.tanggal_penjualan}}</td>
                                                <td width="18%">{{item.total_harga}}</td>
                                                <!-- <td width="18%"><a :href="item.link_invoice">{{item.link_invoice}}</a></td> -->
                                                <td width="18%">{{item.email}}</td>
                                                <td width="18%">{{item.status}}</td>
                                                <td width="10%"><router-link :to="'/detail_penjualan_sedang_dikirim/'+item.id_penjualan">Detail</router-link></td>
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
      const penjualan_sedang_dikirim = ref([])
        onMounted(() =>{
            axios.get('http://192.168.43.22:8000/api/admin/penjualan_dikirim').then(response =>{
                console.log(response.data)
                penjualan_sedang_dikirim.value = response.data.barang
            })
        })
        return {
                penjualan_sedang_dikirim
            }
    }
}
</script>
<style>

</style>

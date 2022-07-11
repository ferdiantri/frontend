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
                        <h1 class="h3 mb-0 text-gray-800">Banner</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Data Banner</h6>
                                </div>
                                    <div class="card-body">
                                        <table class="table">
                                            <tr>
                                                <td width="20%" align="center">Gambar</td>
                                                <td width="20%" align="center">Id Banner</td>
                                                <td width="20%" align="center">Konten Banner</td>
                                                <td width="20%" align="center">Operasi</td>
                                            </tr>
                                        </table>
                                        <table class="table">
                                            <tr v-for="item in banner" :key="item.id_banner">
                                                <td width="20%" align="center"><img :src="'http://192.168.43.22:8000/'+item.gambar" width="100" height="100"></td>
                                                <td width="20%" align="center">{{item.id_banner}}</td>
                                                <td width="20%" align="center">{{item.content_banner}}</td>
                                                <td width="20%" align="center"><router-link :to="'/detail_banner/'+item.id_banner">Detail Banner</router-link></td>
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
      const banner = ref([])
        onMounted(() =>{
            const token = localStorage.getItem('token')
            const headers = {
                'Authorization': 'Bearer '+token
            }
            axios.get('http://192.168.43.22:8000/api/admin/banner', headers).then(response =>{
                console.log(response.data)
                banner.value = response.data
            })
        })
        return {
                banner
            }
    }
}
</script>
<style>

</style>

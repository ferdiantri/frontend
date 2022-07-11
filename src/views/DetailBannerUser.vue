<template>
<div id="wrapper">

        <!-- Sidebar -->
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                </div>
                                    <div class="card-body">
                                        <table class="table" v-for="item in detail_banner" :key="item.id_banner">
                                            <tr>
                                                <td align="center"><img :src="'http://192.168.43.22:8000/'+item.gambar" width="390"></td>
                                            </tr>
                                            <tr>
                                                <td>{{item.content_banner}}</td>
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
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->

    </div>

  
</template>
<script>
import { onMounted, ref } from 'vue'
import axios from 'axios';
export default {
  name: 'App',
  components: {
  },
  setup() {
      const detail_banner = ref([])

      const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
        onMounted(() =>{
            let path = window.location.pathname;
            let segments = path.split("/");
            console.log(segments[2]);
            axios.get(`http://192.168.43.22:8000/api/admin/detail_banner?id_banner=${segments[2]}`).then(response =>{
                console.log(response.data)
                detail_banner.value = response.data
            })
            
        })
        return {
                detail_banner,
                numberFormat
            }
    }
};
</script>
<style>

</style>

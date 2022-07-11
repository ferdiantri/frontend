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
                        <h1 class="h3 mb-0 text-gray-800">Tambah Banner</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <div v-if="UploadFailed" class="alert alert-danger">
                                    {{UploadError}}
                            </div>
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Masukkan Data Banner Promo</h6>
                                </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="preview" v-if="preview">
                                                <img :src="preview" width="700" height="300">
                                            </div>
                                            <input type="file" class="form-control"
                                                id="" @change="fileImage"
                                                placeholder="Pilih Gambar" name="gambar" required>
                                        </div>
                                        <div class="form-group">                     
                                            <textarea class="form-control form-control-user" v-model="content_banner" 
                                                placeholder="Masukkan Deskripsi" required rows="4" cols="50"></textarea>
                                        </div> 
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-user btn-block" @click="tambahBanner">Tambah Banner</button>
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
  data(){
      return{
      gambar: "",
      content_banner: "",
      preview: null,
      UploadFailed: null
    }
  },
  methods: {
      fileImage(event){  
      this.gambar = event.target.files[0];  
      this.preview = URL.createObjectURL(event.target.files[0]);      
    },
     tambahBanner(){
        const config = {
            headers: {'content-type': 'multipart/form-data'},
        }
        try {
            const token = localStorage.getItem('token')
            axios.get('http://192.168.43.22:8000/api/admin/profile', {headers: {'Authorization': 'Bearer '+token}})
                .then(response => {
                    this.email = response.data.profile.email
                    var formData = new FormData()
                    formData.append('gambar', this.gambar)
                    formData.append('content_banner', this.content_banner)
                    formData.forEach(el => console.log(el))

                    axios.post('http://192.168.43.22:8000/api/admin/tambah_banner', formData, config)
                    .then(response => {
                        if(response.data.error){
                                this.UploadFailed = true
                                this.UploadError = response.data;
                            }
                            else{ 
                                this.$router.push({
                                name: "Dashboard",
                                })
                            }
                    })     
                })                    
      }catch(err){
        console.log(err);
      }
      }
  }
};
</script>
<style>

</style>

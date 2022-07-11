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
                        <h1 class="h3 mb-0 text-gray-800">Tambah Barang</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <div v-if="UploadFailed" class="alert alert-danger">
                                    {{UploadError}}
                            </div>
                            <div v-if="BarangEmpty" class="alert alert-danger">
                                    {{BarangError}}
                            </div>
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Masukkan Data Barang</h6>
                                </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <div class="preview" v-if="preview">
                                                <img :src="preview" width="300" height="300">
                                            </div>
                                            <input type="file" class="form-control"
                                                id="" @change="fileImage"
                                                placeholder="Pilih Gambar" name="gambar" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="nama_barang" @input="cekBarang"
                                                 placeholder="Masukkan Nama Barang" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" class="form-control form-control-user" v-model="ram" @input="cekBarang"
                                                placeholder="Masukkan Ram" required>
                                        </div> 
                                        <div class="form-group">
                                            <input type="number" class="form-control form-control-user" v-model="internal" @input="cekBarang"
                                                placeholder="Masukkan Internal" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="warna" @input="cekBarang"
                                                placeholder="Masukkan Warna" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="kamera_depan" 
                                                placeholder="Masukkan Kamera Depan" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="kamera_belakang" 
                                                placeholder="Masukkan Kamera Belakang" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="layar" 
                                                placeholder="Masukkan layar" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="chipset" 
                                                placeholder="Masukkan Chipset" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="baterai" 
                                                placeholder="Masukkan Baterai" required>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-user btn-block" @click="tambahBarang">Tambah Barang</button>
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
      nama_barang: "",
      ram: "",
      internal: "",
      warna: "",
      kamera_belakang: "",
      kamera_depan: "",
      layar: "",
      chipset: "",
      baterai: "",
      email: "", 
      preview: null,
      UploadFailed: "",
      UploadError: "",
      BarangError: null,
      BarangEmpty: null,

    }
  },
  methods: {
      cekBarang(){
        try{
            axios.post('http://192.168.43.22:8000/api/admin/cek_kesamaan_barang', {
                nama_barang: this.nama_barang,
                ram: this.ram,
                internal: this.internal,
                warna: this.warna
            })
            .then(response => {
                if(response.data.error){
                    console.log('Barang Sudah Terdaftar');
                    this.BarangEmpty = true
                    this.BarangError = 'Barang Sudah terdaftar';
                }
                if(response.data.success){
                    console.log('Barang Belum Terdaftar');
                    this.BarangEmpty = false
                    this.BarangError = 'Barang Belum terdaftar';
                }
            })
        }
        catch(err){
            console.log(err);
        }
    },
      fileImage(event){  
      this.gambar = event.target.files[0];  
      this.preview = URL.createObjectURL(event.target.files[0]);      
    },
     tambahBarang(){
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
                    formData.append('nama_barang', this.nama_barang)
                    formData.append('ram', this.ram)
                    formData.append('internal', this.internal)
                    formData.append('warna', this.warna)
                    formData.append('kamera_depan', this.kamera_depan)
                    formData.append('kamera_belakang', this.kamera_belakang)
                    formData.append('layar', this.layar)
                    formData.append('chipset', this.chipset)
                    formData.append('baterai', this.baterai)
                    formData.append('email', this.email)
                    formData.forEach(el => console.log(el))

                    axios.post('http://192.168.43.22:8000/api/admin/tambah_barang', formData, config)
                    .then(response => {
                        console.log(response.data)
                        
                        if(response.data){
                            this.UploadFailed = true
                            this.UploadError = response.data;
                        }
                        if(response.data.error){
                            this.UploadFailed = true
                            this.UploadError = response.data.error;
                        }
                        if(response.data.success){
                            alert('Berhasil');
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

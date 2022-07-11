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
                        <h1 class="h3 mb-0 text-gray-800">Tambah Voucher</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <div class="col-xl-12 col-lg-7">
                            <!-- <div v-if="UploadFailed" class="alert alert-danger">
                                    {{UploadError}}
                            </div> -->
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->    
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Masukkan Data Voucher</h6>
                                </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="kode" 
                                                 placeholder="Masukkan Kode Voucher" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" class="form-control form-control-user" v-model="min_pembelian" 
                                                placeholder="Masukkan Minimal Pembelian" required>
                                        </div> 
                                        <div class="form-group">
                                            <input type="number" class="form-control form-control-user" v-model="potongan" 
                                                placeholder="Masukkan Potongan" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="kuota" 
                                                placeholder="Masukkan Kuota Voucher" required>
                                        </div>
                                        <div class="form-group">
                                            <p>Tanggal Mulai</p>
                                            <input type="date" class="form-control form-control-user" v-model="tanggal_mulai" 
                                                placeholder="Tanggal Mulai" required>
                                        </div>
                                        <div class="form-group">
                                            <p>Tanggal Selesai</p>
                                            <input type="date" class="form-control form-control-user" v-model="tanggal_selesai" 
                                                placeholder="Tanggal Selesai" required>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-user btn-block" @click="tambahVoucher">Tambah Voucher</button>
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
          kode: "",
          min_pembelian: "",
          potongan: "",
          kuota: "",
          tanggal_mulai: "",
          tanggal_selesai: "",
          email: ""
      }
  },
  methods: {
    tambahVoucher(){
    var date = new Date().valueOf()
    var mulai = new Date(this.tanggal_mulai).valueOf()
    var selesai = new Date(this.tanggal_selesai).valueOf()
    // const diffTime = Math.abs(date - mulai);
            if(date >= mulai){
                alert('Tanggal Mulai Harus Lebih Dari ', date);
                console.log(date.valueOf(), 'date')
                console.log(mulai.valueOf(), 'mulai')
                console.log(selesai.valueOf(), 'selesai')
                // console.log(this.tanggal_mulai, 'tgmulai')
                // console.log(diffTime)

            }
            else if(date >= selesai || selesai <= mulai){
                alert('Tanggal Selesai Harus Lebih ',date, ' Dan ', this.tanggal_mulai);
                console.log(date.valueOf(), 'date')
                console.log(mulai.valueOf(), 'mulai')
                console.log(selesai.valueOf(), 'selesai')
            }
            else if(this.kode == '' || this.min_pembelian == '' || this.potongan == ''|| this.kuota == '' || this.tanggal_mulai == '' || this.tanggal_selesai == ''){
                alert('Data Harus Diisi');
            }
            else{
                console.log('OK',date);
                try{
                    const token = localStorage.getItem('token')
                    axios.get('http://192.168.43.22:8000/api/admin/profile', {headers: {'Authorization': 'Bearer '+token}})
                    .then(response => {
                        this.email = response.data.profile.email

                        axios.post('http://192.168.43.22:8000/api/admin/tambah_voucher', {
                            kode: this.kode, min_pembelian: this.min_pembelian, 
                            potongan: this.potongan, kuota: this.kuota, tanggal_mulai: this.tanggal_mulai,
                            tanggal_selesai: this.tanggal_selesai, email: this.email
                            })
                        .then(response => {
                            if(response.data.error){
                                this.UploadFailed = true
                                this.UploadError = response.data;
                            }
                            else{
                                alert('Berhasil')
                                this.$router.push({
                                name: "Dashboard",
                                })
                            }
                        })     
                    })           
                }
                catch(err){
                    console.log(err);
                }
            }
        }
    }
}
</script>
<style>

</style>

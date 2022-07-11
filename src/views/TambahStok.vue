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
                        <h1 class="h3 mb-0 text-gray-800">Stok</h1>
                        <div v-if="BarangError" class="alert alert-danger">
                            {{BarangEmpty}}
                        </div>
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
                                    <h6 class="m-0 font-weight-bold text-primary">Masukkan Data Pembelian</h6>
                                    <div v-if="BarangError">
                                        <router-link :to="'/tambah_barang'"><button class="btn btn-primary">Tambah Barang</button></router-link>
                                    </div>
                                </div>
                                    <div class="card-body">
                                    <div v-for="(dataSelectBarang, index) in dataSelectBarang" :key="index">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="search[`${index}`]" :name="`search[${index}]`" @input="cekBarang"
                                                 placeholder="Cari Nama Barang" required>
                                        </div>
                                        <div class="form-group">
                                            <select class="form-control" v-model="dataSelectBarang.id_barang">
                                                <option value="" disabled selected>Pilih Barang</option>
                                                <option v-for="item in dataBarang[index]" :value="{ id_barang: item.id_barang, nama_barang: item.nama_barang, ram: item.ram, internal: item.internal, warna: item.warna, gambar: item.gambar }" :key="item.id_barang">
                                                    {{ item.nama_barang }} {{ item.ram }}GB/{{ item.internal }}GB - {{ item.warna }}  
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-row"  v-if="dataSelectBarang.id_barang.gambar !== undefined">
                                            <div class="form-group col-md-3">
                                                <div class="form-group">
                                                    <img :src="'http://192.168.43.22:8000/'+dataSelectBarang.id_barang.gambar" width="100" height="100">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="form-group">
                                                    <input type="number" class="form-control form-control-user" v-model="jumlah_barang[`${index}`]" :name="`jumlah_barang[${index}]`"
                                                    placeholder="Masukkan Jumlah Barang" required>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="form-group">
                                                    <input type="number" class="form-control form-control-user" v-model="harga_beli[`${index}`]" :name="`harga_beli[${index}]`"
                                                    placeholder="Masukkan Harga Beli" required>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-3">
                                                <div class="form-group">
                                                    <input type="number" class="form-control form-control-user" v-model="harga_jual[`${index}`]" :name="`harga_jual[${index}]`"
                                                        placeholder="Masukkan Harga Jual" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                        <hr>
                                    </div>
                                        <div class="form-group">
                                            <button class="btn btn-success btn-user btn-block" @click="tambahBarang">Tambah Barang</button>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary btn-user btn-block" @click="tambahPembelian">Tambah Pembelian</button>
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
          dataBarang : [],
          dataSelectBarang : [{
            nama_barang : "",
            ram: "",
            internal: "",
            warna: "",
            id_barang: "",
          }],
          harga_beli: [],
          harga_jual: [],
          jumlah_barang: [],
          email: "",
          search: [],
          BarangEmpty: "",
          BarangError: ""
      }
  },
  methods: {
    tambahBarang(){
        this.dataSelectBarang.push({
            nama_barang : "",
            ram: "",
            internal: "",
            warna: "",
            id_barang: "",
            jumlah_barang: "",
            harga_beli: "",
            harga_jual: "",
        })
    },
    cekBarang(){
        try{
            for(const key in this.search){
                console.log(this.search[key])
                axios.post('http://192.168.43.22:8000/api/admin/cek_nama_barang', {
                    nama_barang: this.search[key]
                    })
                .then(response => {
                    this.dataBarang[key] = response.data
                    console.log(response.data);
                    if(response.data.length == 0){
                        console.log('Barang tidak terdaftar');
                        this.BarangError = true
                        this.BarangEmpty = 'Barang tidak terdaftar';
                    }
                    else{
                        console.log('Barang terdaftar');
                        this.BarangError = false;
                    }
                })
            }
        }
        catch(err){
            console.log(err);
        }
    },
    
    tambahPembelian(){
        if(this.dataSelectBarang.id_barang == "" || this.dataSelectBarang.nama_barang == "" ||this.dataSelectBarang.ram == "" 
        || this.dataSelectBarang.internal == "" || this.dataSelectBarang.warna == "" 
        || this.jumlah_barang == "" || this.harga_jual == "" || this.harga_beli == ""){
            alert("Data harus diisi!");
        }
        else{
            try{
                const token = localStorage.getItem('token')
                axios.get('http://192.168.43.22:8000/api/admin/profile', {headers: {'Authorization': 'Bearer '+token}})
                .then(response => {
                    this.email = response.data.profile.email
                    for(const key in this.dataSelectBarang.map(item => item.id_barang.id_barang)){
                        const data = {
                            id_barang: this.dataSelectBarang.map(item => item.id_barang.id_barang)[key],
                            nama_barang: this.dataSelectBarang.map(item => item.id_barang.nama_barang)[key],
                            ram : this.dataSelectBarang.map(item => item.id_barang.ram)[key],
                            internal: this.dataSelectBarang.map(item => item.id_barang.internal)[key],
                            warna: this.dataSelectBarang.map(item => item.id_barang.warna)[key],
                            jumlah_barang: this.jumlah_barang[key],
                            harga_beli: this.harga_beli[key],
                            harga_jual: this.harga_jual[key],
                            email: this.email,
                        };
                    
                    axios.post('http://192.168.43.22:8000/api/admin/tambah_pembelian', data)
                    .then(response => {
                        if(response.data.pembelian){
                            alert('Berhasil')
                            this.$router.push({
                                name: "Stok",
                                })
                            }
                            else{
                                this.UploadFailed = true
                                this.UploadError = response.data;
                            }
                        })     
            }})           
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

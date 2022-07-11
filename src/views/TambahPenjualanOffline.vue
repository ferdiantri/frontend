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
                        <h1 class="h3 mb-0 text-gray-800">Penjualan Offline</h1>
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
                                    <h6 class="m-0 font-weight-bold text-primary">Masukkan Data Penjualan</h6>
                                </div>
                                <table class="table">
                                    <tr>
                                        <td class="col-md-6">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <input type="text" class="form-control form-control-user" v-model="nama_pembeli"
                                                        placeholder="Masukkan Nama Pembeli" required>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" class="form-control form-control-user" v-model="alamat" 
                                                        placeholder="Masukkan Alamat" required>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" class="form-control form-control-user" v-model="nomor_telepon" 
                                                        placeholder="Masukkan Nomor Telepon" required>
                                                </div>
                                                <div v-for="(barang, index) in barang" :key="index">
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control form-control-user" @input="cekBarang()" v-model="search[`${index}`]" :name="`search[${index}]`" 
                                                                placeholder="Cari Nama Barang" required>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-4">
                                                            <select class="form-control" v-model="barang.id_barang" :name="`barang[${index}][id_barang]`">
                                                                <option value="" disabled selected>Pilih Barang</option>
                                                                <option v-for="item in dataNama[`${index}`]" :value="{ id_barang: item.id_barang, nama_barang: item.nama_barang,
                                                                stok_barang: item.stok_barang, harga: item.harga , ram: item.ram, internal: item.internal, warna: item.warna}" :key="item.id_barang">
                                                                    {{ item.nama_barang }} {{item.ram}}GB/{{item.internal}}GB - {{item.warna}}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-3">
                                                            <select class="form-control" v-model="barang.jumlah_barang" :name="`barang[${index}][jumlah_barang]`">
                                                                <option value="" disabled selected>Pilih Jumlah Barang</option>
                                                                <option v-for="item of barang.id_barang.stok_barang" :value="item" :key="item">
                                                                    {{item}}
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-1">
                                                            <button @click="removeBarang(index)" type="button" class="btn"><i class="fa fa-trash fa-lg"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <button @click="addBarang" type="button" class="btn btn-secondary">Tambah Barang</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col-md-6">
                                            <h4>Detail Harga</h4>
                                            <table id="detail_barang" class="table table-bordered">
                                                <tr>
                                                    <td>Id Barang</td>
                                                    <td>Nama Barang</td>
                                                    <td>Harga</td>
                                                    <td>Jumlah Barang</td>
                                                    
                                                </tr>
                                                <tr v-for="item in barang" :key="item.id_barang">
                                                    <td v-if="item.id_barang.id_barang != null">{{item.id_barang.id_barang}}</td>
                                                    <td v-if="item.id_barang.nama_barang != null">{{item.id_barang.nama_barang}} {{item.id_barang.ram}}GB/{{item.id_barang.internal}}GB - {{item.id_barang.warna}}</td>
                                                    <td v-if="item.id_barang.harga != null">{{numberFormat(item.id_barang.harga)}}</td>
                                                    <td v-if="item.jumlah_barang != null" width="10%">{{item.jumlah_barang}}</td>
                                                </tr> 
                                            </table>
                                            <table id="detail_harga" class="table table-bordered">
                                                <tr>
                                                    <td align="right">Total Barang</td>
                                                    <td align="right">{{numberFormat(barang.map(item => item.id_barang.harga*item.jumlah_barang).reduce((x, y) => (x + y), 0))}}</td>
                                                </tr>
                                            </table>
                                            <div class="form-group">
                                                <button class="btn btn-primary btn-user btn-block" @click="tambahPembelian">Tambah Penjualan</button>
                                            </div>
                                        </td>
                                        <table class="d-none" id="alamat">
                                            <tr>
                                                <td>Mitra Cell</td>
                                                <td>{{nama_pembeli}} ({{nomor_telepon}})</td>
                                            </tr>
                                            <tr>
                                                <td>Jl. Kimia Farma no. 201</td>
                                                <td>{{alamat}}</td>
                                            </tr>
                                            <tr>
                                                <td>Kesamben, Kabupaten Jombang</td>
                                            </tr>
                                            <tr>
                                                <td>Jawa Timur, 61484</td>
                                            </tr>
                                        </table>
                                        <table class="d-none" id="id">
                                            <tr>
                                                <td></td>
                                            </tr>
                                        </table>
                                    </tr>
                                </table>
                                        <!-- <h1>{{barang.map(item => item.id_barang.harga*item.jumlah_barang).reduce((x, y) => (x + y), 0)}}</h1> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>r
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
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
export default {
  name: 'App',
  components: {
    Footer,
    Sidebar,
    Topbar,
  },
  data: () => ({
    dataNama: [],
    penjualan: [],
    UploadFailed: false,
    UploadError: "",
    search: [],
    nama_pembeli: "",
    alamat : "",
    nomor_telepon: "",
    barang: [
      {
        id_barang: "",
        nama_barang: "", 
        jumlah_barang: "",
        harga: "",
        ram: "",
        internal: "",
        warna: "",
      },
    ],
  }),
  setup(){
      const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(value);
    return {
        numberFormat
    }
  },
  methods: {
    async createPDF () {
        let pdfName = `${this.penjualan.map(item => item.id_penjualan_offline)[0]}, ${this.nama_pembeli}`; 
        var doc = new jsPDF();
        autoTable(doc, { html: '#id', theme: 'plain'});
        autoTable(doc, { html: '#alamat', theme: 'plain'});
        autoTable(doc, { html: '#detail_barang', theme: 'grid' });
        autoTable(doc, { html: '#detail_harga', theme: 'grid' });
        doc.text("Invoice Penjualan", 15, 10);
        doc.text(this.penjualan.map(item => item.id_penjualan_offline)[0], 15, 15);
        doc.save(pdfName + '.pdf');
    },
    addBarang () {
      this.barang.push({
        id_barang: "",
        nama_barang: "", 
        jumlah_barang: "",
        harga: "",
        ram: "",
        internal: "",
        warna: "",
      })
    },
    removeBarang (index) {
      this.barang.splice(index, 1)
    },
    cekBarang(){
        try{
            for(const key in this.search){
                console.log(this.search[key])
                axios.post('http://192.168.43.22:8000/api/admin/cek_barang', {
                    nama_barang: this.search[key]
                    })
                .then(response => {
                    console.log(response.data);
                    this.dataNama[key] = response.data
                })
            }
        }
        catch(err){
            console.log(err);
        }
    },
    tambahPembelian(){
        try{
            if(this.nama_pembeli == "" || this.alamat == "" || this.nomor_telepon == "" || 
            this.barang.map(item => item.id_barang.id_barang) == "" || this.barang.map(item => item.jumlah_barang) == ""){
                alert("Semua data harus diisi!");
            }
            else{
                const token = localStorage.getItem('token')
                axios.get('http://192.168.43.22:8000/api/admin/profile', {headers: {'Authorization': 'Bearer '+token}})
                .then(response => {
                    this.email = response.data.profile.email
                    const data = {
                        nama_pembeli: this.nama_pembeli,
                        alamat: this.alamat,
                        nomor_telepon: this.nomor_telepon,
                        id_barang: this.barang.map(item => item.id_barang.id_barang),
                        jumlah_barang: this.barang.map(item => item.jumlah_barang),
                        email: this.email
                    } 
                    axios.post('http://192.168.43.22:8000/api/admin/tambah_penjualan_offline', data)
                    .then(response => {
                        if(response.data.penjualan){
                            this.penjualan = response.data.penjualan;
                            this.createPDF();
                            if(this.penjualan.map(item => item.id_penjualan_offline)){
                                alert('Berhasil');
                                this.$router.push({
                                    name: "PenjualanOffline",
                                })
                            }
                        }
                        else{
                            this.UploadFailed = true
                            this.UploadError = response.data;
                        }
                    })     
                })
            }
                       
        }
        catch(err){
            console.log(err);
        }
        }
    }
}
</script>
<style>

</style>

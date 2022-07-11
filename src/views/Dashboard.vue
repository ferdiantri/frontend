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
                    

                    <!-- Content Row -->
                    <div class="row">
                        <div class="form-group col-md-3">
                            <div class="form-group">
                                <select class="form-control" v-model="bulan" @change="sort">
                                    <option value="" disabled selected>Pilih Bulan</option>   
                                    <option value="1">Januari</option>  
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <div class="form-group">
                                <select class="form-control" v-model="tahun" @change="sort">
                                    <option value="" disabled selected>Pilih Tahun</option> 
                                    <option value="2022">2022</option>   
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>   
                                    <option value="2025">2025</option>   
                                    <option value="2026">2026</option>     
                                </select>
                            </div>
                        </div>
                        <!-- <div class="form-group col-md-3">
                            <div class="form-group">
                                <div class="form-group">
                                    <button class="btn btn-primary btn-user btn-block" @click="sort">Sort</button>
                                </div>
                            </div>
                        </div> -->
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Pembelian</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{numberFormat(total_pembelian)}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Penjualan</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{numberFormat(total_penjualan)}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Penjualan Masuk</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{numberFormat(total_penjualan_masuk)}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Pesanan Dibayar</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{total_penjualan_dibayar}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Pesanan Diproses</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{total_penjualan_diproses}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Pesanan Dikirim</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{total_penjualan_dikirim}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Pesanan Terkirim</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{total_penjualan_terkirim}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8 col-lg-7">

                            <!-- Area Chart -->
                            <!-- <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Bar Chart</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <Bar
                                            :chart-options="chartOptions"
                                            :chart-data="chartData"
                                            :chart-id="chartId"
                                            :dataset-id-key="datasetIdKey"
                                            :plugins="plugins"
                                            :css-classes="cssClasses"
                                            :styles="styles"
                                            :width="width"
                                            :height="height"
                                        />
                                    </div>
                                </div>
                            </div>     -->
                            <!-- Bar Chart -->

                        </div>

                        <!-- Donut Chart -->
                        
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
import { onMounted, ref} from 'vue'
import Footer from '@/components/Footer.vue';
import Sidebar from '@/components/Sidebar.vue';
import Topbar from '@/components/Topbar.vue';
import axios from 'axios';
// import { Bar } from 'vue-chartjs'
// import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

// ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
export default {
  name: 'App',
  components: {
    Footer,
    Sidebar,
    Topbar,
    // Bar,
  },
  props: {
    // chartId: {
    //   type: String,
    //   default: 'bar-chart'
    // },
    // datasetIdKey: {
    //   type: String,
    //   default: 'label'
    // },
    // width: {
    //   type: Number,
    //   default: 400
    // },
    // height: {
    //   type: Number,
    //   default: 200
    // },
    // cssClasses: {
    //   default: '',
    //   type: String
    // },
    // styles: {
    //   type: Object,
    //   default: () => {}
    // },
    // plugins: {
    //   type: Object,
    //   default: () => {}
    // }
  },
  data(){
    // const date = new Date();
    // let bulan = date.getMonth();
    // let tahun = date.getFullYear();
      return {    
        // tahun: tahun,
        // bulan: bulan
    //   chartData: {
    //     labels: [ 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' ],
    //     datasets: [ { data: [10, 20, 12] } ]
    //   },
    //   chartOptions: {
    //     responsive: true
    //   }
    }
  },
  setup() {
      const date = new Date();
      const total_penjualan = ref([])
      const total_pembelian = ref([])
      const total_penjualan_dibayar = ref([])
      const total_penjualan_diproses = ref([])
      const total_penjualan_dikirim = ref([])
      const total_penjualan_terkirim = ref([])
      const total_penjualan_masuk = ref([])
      const bulan = ref(date.getMonth()+1)
      const tahun = ref(date.getFullYear())
      const numberFormat = (value) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        }).format(value);
        
        function sort() { 
            const data = {
                tahun: tahun.value,
                bulan: bulan.value
            }
            console.log(data)
            axios.post('http://192.168.43.22:8000/api/admin/total_penjualan', data).then(response =>{
                console.log(response.data.barang)
                total_penjualan.value = response.data.barang.map(item => item.total_penjualan)[0]
            })
            axios.post('http://192.168.43.22:8000/api/admin/total_pembelian', data).then(response =>{
                console.log(response.data.barang)
                total_pembelian.value = response.data.barang.map(item => item.total_harga)[0]
            })
            axios.post('http://192.168.43.22:8000/api/admin/total_penjualan_dibayar', data).then(response =>{
                console.log(response.data.barang)
                total_penjualan_dibayar.value = response.data.barang
            })
            axios.post('http://192.168.43.22:8000/api/admin/total_penjualan_diproses', data).then(response =>{
                console.log(response.data.barang)
                total_penjualan_diproses.value = response.data.barang
            })
            axios.post('http://192.168.43.22:8000/api/admin/total_penjualan_dikirim', data).then(response =>{
                console.log(response.data.barang)
                total_penjualan_dikirim.value = response.data.barang
            })
            axios.post('http://192.168.43.22:8000/api/admin/total_penjualan_terkirim', data).then(response =>{
                console.log(response.data.barang)
                total_penjualan_terkirim.value = response.data.barang
            })
            axios.post('http://192.168.43.22:8000/api/admin/total_penjualan_masuk', data).then(response =>{
                console.log(response.data.barang)
                total_penjualan_masuk.value = response.data.barang.map(item => item.total_penjualan)[0]
            })
        }
        

        onMounted(() =>{
            sort()
        })
        return {
                total_penjualan,
                total_pembelian,
                total_penjualan_dibayar,
                total_penjualan_diproses,
                total_penjualan_dikirim,
                total_penjualan_terkirim,
                total_penjualan_masuk,
                numberFormat,
                sort,
                bulan,
                tahun
            }
    },
    // methods: {
    //     async sort() {
    //         console.log(this.tahun);
    //         console.log(this.bulan);
    //     }
    // },
};
</script>
<style>

</style>

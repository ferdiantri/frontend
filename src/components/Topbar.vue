<template>
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span v-if="badge > 0" class="badge badge-danger badge-counter">{{badge}}</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div v-if="badge > 0" class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Notifikasi
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" v-on:click="onAlert(item.id_notifikasi)" v-for="item in notifikasi" :key="item.id_notifikasi">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">{{item.tanggal}}</div>
                                        <span class="font-weight-bold">{{item.pesan}}</span>
                                    </div>
                                </a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ user.email }} <i class="fas fa-fw fa-power-off" @click="logout"></i></span>
                            </a>
                            <!-- Dropdown - User Information -->
          
                                
                    
                        </li>

                    </ul>

                </nav>
</template>
<script>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

    export default {

        setup() {

            //state token
            const token = localStorage.getItem('token')

            //inisialisasi vue router on Composition API
            const router = useRouter()

            //state user
            const user = ref('')
            const notifikasi = ref('')
            const badge = ref('')
            //mounted properti
            onMounted(() =>{

                //check Token exist
                if(!token) {
                    return router.push({
                        name: 'Login'
                    })
                }
                
                //get data user
                axios.defaults.headers.common.Authorization = `Bearer ${token}`
                axios.get('http://192.168.43.22:8000/api/admin/profile')
                .then(response => {

                    //console.log(response.data.name)
                    user.value = response.data.profile
                    localStorage.setItem("email", response.data.profile.email)
                })
                axios.get('http://192.168.43.22:8000/api/admin/notifikasi')
                .then(response => {
                    //console.log(response.data.name)
                    notifikasi.value = response.data.notifikasi
                    badge.value = response.data.badge

                })
                .catch(error => {
                    console.log(error.response)
                })

            })

            //method logout
            function logout() {

                //logout
                axios.get('http://192.168.43.22:8000/api/admin/logout')
                .then(response => {
                    console.log(response);
                    localStorage.removeItem('token')
                    if(!localStorage.removeItem('token')) {

                        //remove localStorage
                        

                        //redirect ke halaman login
                        return router.push({
                            name: 'Login'
                        })

                    }

                })
                .catch(error => {
                    console.log(error.response.data)
                })

            }
            function onAlert(id_notifikasi) {
                const data = {
                    id_notifikasi: id_notifikasi
                }
                axios.post('http://192.168.43.22:8000/api/admin/update_notifikasi', data)
                .then(response => {
                    if(response.data.success){
                        axios.get('http://192.168.43.22:8000/api/admin/notifikasi')
                        .then(response => {
                    //console.log(response.data.name)
                        notifikasi.value = response.data.notifikasi
                        badge.value = response.data.badge

                })
                    }
                })
            }
        

            return {
                token,
                badge,
                notifikasi,      // <-- state token
                user,       // <-- state user
                logout,
                onAlert     // <-- method logout
            }

        }
    }
</script>

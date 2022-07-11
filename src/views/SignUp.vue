<template>
<div class="bg-gradient-primary">
  <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div>
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Selamat Datang Kembali!</h1>
                                    </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" v-model="nama"
                                                placeholder="Masukkan Nama Lengkap" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp" v-model="email"
                                                placeholder="Masukkan Email" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="number" class="form-control form-control-user" v-model="nomor_telepon"
                                                placeholder="Masukkan Nomor Telepon" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" v-model="password" 
                                                id="exampleInputPassword" placeholder="Masukkan Password" required>
                                        </div>                                     
                                        <button class="btn btn-primary btn-user btn-block" @click="signup">Sign Up</button>
                                    <hr>
                                    
                                    <div class="text-center">
                                        <a class="small" href="/">Login!</a>
                                    </div>
                                </div>
                            </div>
                </div>

            </div>

        </div>
        </div>
    </div>
</div>
</template>
<script>
import axios from "axios";
  
export default {
  name: "signup",
  data() {
    return {
      nama: "",
      email: "",
      nomor_telepon: "",
      password: "",
      user: [],
      validation: [],
      signupFailed: null
    };
  },
  methods: {
    async signup() {
      try {
        await axios
          .post('http://192.168.43.22:8000/api/admin/signup', { nama: this.nama, nomor_telepon: this.nomor_telepon, email: this.email, password: this.password})
          .then(response => {
                            if(response.data.success){
                            this.$router.push({
                                                name: "Login",
                                                })
                            }
                            else{
                                this.signupFailed = true
                                this.signupError = response.data;
                            }
                            })
                            
      }catch(err){
        console.log(err);
      }
      this.validation = []
      if (!this.user.nama) {
        this.validation.nama = true
      }
      
      if (!this.user.email) {
        this.validation.email = true
      }

      if (!this.user.nomor_telepon) {
        this.validation.nomor_telepon = true
      }

      if (!this.user.password) {
        this.validation.password = true
      }
    },
  },
};
</script>
  
<style>
</style>
<template>
<div class="bg-gradient-default">
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
                                            <input type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" aria-describedby="emailHelp" v-model="email"
                                                placeholder="Masukkan Email" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" v-model="password" 
                                                id="exampleInputPassword" placeholder="Masukkan Password" required>
                                        </div>                                     
                                        <button class="btn btn-primary btn-user btn-block" @click="login()">Log in</button>
                                    <hr>
                                    
                                    <div class="text-center">
                                        <a class="small" href="/signup">Sign Up!</a>
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
  name: "login",
  data() {
    return {
      email: "",
      password: "",
      token: localStorage.getItem('token'),
      user: [],
      validation: [],
      loginFailed: null

    };
  },
  methods: {
    async login() {
      try {
        await axios
          axios.post("http://192.168.43.22:8000/api/admin/login", { email: this.email, password: this.password})
          .then(response => { console.log(response.data.token)
            if (response.status) {
              localStorage.setItem("token", response.data.token)
              this.$router.push({
                name: "Dashboard",
              })
            }else{
              this.$router.push({
                name: "Login",
              })
            }  
          })
      }catch(err){
        console.log(err);
      }
      this.validation = []
      if (!this.user.email) {
        this.validation.email = true
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
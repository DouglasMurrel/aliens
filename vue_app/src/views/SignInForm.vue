<template>
  <div class="container">
    <h1>Sign In Form</h1>

    <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess && userLoaded">
      <div>Congratulations! You successfully signed in as {{ userEmail }}</div>
      <a href='' @click.prevent="logout">Logout</a>
    </div>

    <form method="post" v-on:submit.prevent="submitForm" v-else-if="userLoaded">
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" class="form-control" id="email" v-model="email" aria-describedby="emailHelp" placeholder="Enter email">
        <small class="form-text text-danger" v-if="validationErrors.email">
          {{ validationErrors.email }}
        </small>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" v-model="password" placeholder="Password" autocomplete="off">
        <small class="form-text text-danger" v-if="validationErrors.password">
          {{ validationErrors.password }}
        </small>
      </div>
      <button type="submit" class="btn btn-success">Login</button>
    </form>

    <div v-else>Please wait</div>
  </div>
</template>

<script>
    import axios from 'axios'
    import {API_URL} from '../config.local'

    export default {
        name: 'SignUpForm',
        data () {
            return {
                msg: 'SignInForm',

                email: '',
                password: '',

                validationErrors: {},
                formSubmittedSuccess: false,
                userLoaded: false,
                authToken: localStorage.getItem('authToken'),
                userEmail: ''
            }
        },
        methods: {
            submitForm: function (event) {
                event.preventDefault();

                let component = this;
                let body = {
                    email: this.email,
                    password: this.password,
                };

                axios.create().post(API_URL + '/login', body).then(function (response) {
                    if(response.data.status === 400){
                      component.validationErrors = response.data.errors;
                    }
                    else{
                      component.formSubmittedSuccess = true;
                      component.validationErrors = {};
                      component.userEmail = response.data.user;
                      const token = response.data.token;
                      localStorage.setItem('authToken', token);
                    }
                }).catch(function (error) {
                    let message = 'Internal server error';
                    alert(message);
                    console.log(message);
                    console.log(error.response);
                });
            },
            logout: function(event){
                localStorage.removeItem('authToken');
                this.formSubmittedSuccess = false;
                axios.create().post(API_URL + '/logout',{}).then(function (response) {
                    if(response.status === 200) {
                        component.formSubmittedSuccess = false;
                        component.userEmail = '';
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        beforeMount() {
            if (this.authToken) {
                let component = this;
                const axiosConfig = {
                    headers: {
                        'Authorization': 'Bearer ' + this.authToken,
                    }
                }
                axios.create().post(API_URL + '/userinfo',{},axiosConfig).then(function (response) {
                    if(response.status === 200) {
                        component.formSubmittedSuccess = true;
                        component.userEmail = response.data.user;
                    }
                    component.userLoaded = true;
                }).catch(function (error) {
                    if (error.response.data.code === 401 && error.response.data.message === 'Expired JWT Token') {
                        console.log('Token expired')
                    }
                    console.log(error);
                    component.userLoaded = true;
                });
            } else {
                this.userLoaded = true;
            }
        }
    }
</script>

<style>
  .container {
    padding-top: 50px;
  }
</style>
<template>
  <div class="container">
    <h1>Sign In Form</h1>

    <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess && userLoaded">
      <div>Congratulations! You successfully signed in as {{ userEmail }}</div>
      <div>{{ userData }}</div>
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
                refreshToken: localStorage.getItem('refreshToken'),
                userEmail: '',
                userData: {}
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
                      component.userData = JSON.parse(response.data.userData);
                      const token = response.data.token;
                      localStorage.setItem('authToken', token);
                      const refresh_token = response.data.refresh_token;
                      localStorage.setItem('refreshToken', refresh_token);
                    }
                }).catch(function (error) {
                    let message = 'Internal server error';
                    console.log(message);
                    console.log(error.response);
                });
            },
            logout: function(event){
                localStorage.removeItem('authToken');
                localStorage.removeItem('refreshToken');
                this.formSubmittedSuccess = false;
                let component = this;
                axios.create().post(API_URL + '/logout',{}).then(function (response) {
                    if(response.status === 200) {
                        component.formSubmittedSuccess = false;
                        component.userEmail = '';
                        component.userData = {};
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
                        component.userData = JSON.parse(response.data.userData);
                        component.userLoaded = true;
                    }
                }).catch(function (error) {
                    if (error.response.data.code === 401 && error.response.data.message === 'Expired JWT Token') {
                        component.userLoaded = false;
                        component.formSubmittedSuccess = false;
                        axios.create().post(API_URL + '/token_refresh', {'refresh_token':component.refreshToken}).then(function (response) {
                            if(response.status === 200){
                                component.validationErrors = {};
                                component.userEmail = response.data.user;
                                component.userData = JSON.parse(response.data.userData);
                                const token = response.data.token;
                                localStorage.setItem('authToken', token);
                                const refresh_token = response.data.refresh_token;
                                localStorage.setItem('refreshToken', refresh_token);
                                component.userLoaded = true;
                                component.formSubmittedSuccess = true;
                            }   
                        }).catch(function (error) {
                            component.userLoaded = true;
                            let message = 'Internal server error';
                            console.log(message);
                            console.log(error.response);
                        });
                    }
                    console.log(error);
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
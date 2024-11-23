<template>
  <div class="container">
    <h1>Sign In Form</h1>

    <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess && userLoaded">
      Congratulations! You successfully signed in as {{ userEmail }}
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
                      const token = response.data.token;
                      localStorage.setItem('authToken', token);
                    }
                }).catch(function (error) {
                    let message = 'Internal server error';
                    alert(message);
                    console.log(message);
                    console.log(error.response);
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
                    if(response.status === 401){
                      //ToDo: реализовать переподключение или логаут
                    }
                    else{
                        component.formSubmittedSuccess = true;
                        component.userEmail = response.data.user;
                    }
                    component.userLoaded = true;
                }).catch(function (error) {
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
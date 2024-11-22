<template>
  <div class="container">
    <h1>Sign In Form</h1>

    <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess">
      Congratulations! You successfully signed in
    </div>

    <form method="post" v-on:submit.prevent="submitForm" v-else>
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" class="form-control" id="email" v-model="email" aria-describedby="emailHelp" placeholder="Enter email">
        <small class="form-text text-danger" v-if="validationErrors.email">
          {{ validationErrors.email }}
        </small>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" v-model="password" placeholder="Password">
        <small class="form-text text-danger" v-if="validationErrors.password">
          {{ validationErrors.password }}
        </small>
      </div>
      <button type="submit" class="btn btn-success">Login</button>
    </form>
  </div>
</template>

<script>
    import axios from 'axios';
    import {API_URL} from '../config.local'

    export default {
        name: 'SignUpForm',
        data () {
            return {
                msg: 'SignInForm',

                email: '',
                password: '',

                validationErrors: {},
                formSubmittedSuccess: false
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

                axios.create().post(API_URL + '/api/sign-in-handler', body).then(function (response) {
                    if(response.data.status === 400){
                      component.validationErrors = response.data.errors;
                    }
                    else{
                      component.formSubmittedSuccess = true;
                      component.validationErrors = {};
                    }
                }).catch(function (error) {
                    let message = 'Internal server error';
                    alert(message);
                    console.log(message);
                    console.log(error.response);
                });
            }
        }
    }
</script>

<style>
  .container {
    padding-top: 50px;
  }
</style>
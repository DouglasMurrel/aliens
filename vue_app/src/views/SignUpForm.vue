<template>
  <div class="container">
    <h1>Sign Up Form</h1>

    <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess">
      Congratulations! You account registered successfully. You can log in now
    </div>

    <form method="post" v-on:submit.prevent="submitForm" v-else>
      <h2 v-if="ajaxWaiting">Loading, please wait...</h2>
      <div class="form-group">
        <label for="fullname">Fullname</label>
        <input type="text" class="form-control" id="fullname" v-model="fullname" placeholder="Enter your name">
        <small class="form-text text-danger" v-if="validationErrors.fullname">
          {{ validationErrors.fullname }}
        </small>
      </div>
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" class="form-control" id="email" v-model="email" aria-describedby="emailHelp" placeholder="Enter email">
        <small class="form-text text-danger" v-if="validationErrors.email">
          {{ validationErrors.email }}
        </small>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" autocomplete="off" id="password" v-model="password" placeholder="Password">
        <small class="form-text text-danger" v-if="validationErrors.password">
          {{ validationErrors.password }}
        </small>
      </div>
      <button type="submit" class="btn btn-success">Register</button>
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
                msg: 'SignUpForm',

                fullname: '',
                username: '',
                email: '',
                password: '',

                validationErrors: {},
                formSubmittedSuccess: false
            }
        },
        computed: {
            ajaxWaiting () {
                return this.$store.state.ajaxWaiting
            }
        },
        methods: {
            submitForm: function (event) {
                event.preventDefault();

                let component = this;
                let body = {
                    fullname: this.fullname,
                    username: this.username,
                    email: this.email,
                    password: this.password,
                };

                this.$store.commit('ajaxWaiting', true);
                axios.create().post(API_URL + '/sign-up-handler', body).then(function (response) {
                    if(response.data.status === 400){
                      component.validationErrors = response.data.errors;
                    }
                    else{
                      component.formSubmittedSuccess = true;
                      component.validationErrors = {};
                    }
                    component.$store.commit('ajaxWaiting', false);
                    component.password = '';
                }).catch(function (error) {
                    console.log(error.response);
                    component.validationErrors = error.response.data.errors;
                    component.$store.commit('ajaxWaiting', false);
                    component.password = '';
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
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
        <input :type="showPass ? 'text' : 'password'" class="form-control" autocomplete="off" id="password" v-model="password" placeholder="Password">
        <a v-html="showPass ? 'Спрятать пароль' : 'Показать пароль'" href="" @click.prevent="showPass=!showPass"></a>
        <small class="form-text text-danger" v-if="validationErrors.password">
          {{ validationErrors.password }}
        </small>
      </div>
      <div class="form-group">
        <label for="password1">Repeat password</label>
        <input type="password" class="form-control" autocomplete="off" id="password1" v-model="password1" placeholder="Repeat password">
        <a v-html="showPass1 ? 'Спрятать пароль' : 'Показать пароль'" href="" @click.prevent="showPass1=!showPass1"></a>
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
                showPass: false,
                password1: '',
                showPass1: false,

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

                if (this.password!==this.password1) {
                    this.validationErrors = {'password': 'Passwords are not equal'};
                    return;
                }

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
                }).catch(function (error) {
//                    console.log(error.response);
                    component.validationErrors = error.response.data.errors;
                    component.$store.commit('ajaxWaiting', false);
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
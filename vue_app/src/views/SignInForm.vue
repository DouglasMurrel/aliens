<template>
  <div class="container">
    <h1>Sign In Form</h1>

    <div class="alert alert-success" role="alert" v-if="loggedIn">
      <div>Congratulations! You successfully signed in as {{ userEmail }}</div>
      <div>{{ userData }}</div>
      <a href='' @click.prevent="logout">Logout</a>
    </div>

    <h2 v-if="ajaxWaiting">Loading, please wait...</h2>
    <form method="post" v-on:submit.prevent="submitForm" v-if="!loggedIn && loaded">
      <small class="form-text text-danger" v-if="validationErrors.password">
        {{ validationErrors.password }}
      </small>
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" class="form-control" id="email" v-model="email" aria-describedby="emailHelp" placeholder="Enter email">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" id="password" v-model="password" placeholder="Password" autocomplete="off">
      </div>
      <button type="submit" class="btn btn-success">Login</button>

      <a href="" @click.prevent="vkLogin">or login through VK account</a>
    </form>
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
                authToken: localStorage.getItem('authToken'),
                refreshToken: localStorage.getItem('refreshToken'),
                userEmail: '',
                loaded: true
            }
        },
        computed: {
            userData () {
                return this.$store.state.userData
            },
            loggedIn () {
                return this.$store.state.loggedIn
            },
            ajaxWaiting () {
                return this.$store.state.ajaxWaiting
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

                component.$store.commit('ajaxWaiting', true);
                axios.create().post(API_URL + '/login', body).then(function (response) {
                    component.$store.commit('loggedIn', true);
                    component.$store.commit('ajaxWaiting', false);
                    component.validationErrors = {};
                    component.userEmail = response.data.user;
                    component.$store.commit('setData',JSON.parse(response.data.userData));
                    const token = response.data.token;
                    localStorage.setItem('authToken', token);
                    const refresh_token = response.data.refresh_token;
                    localStorage.setItem('refreshToken', refresh_token);
                }).catch(function (error) {
                    if(error.response.data.code === 401 && error.response.data.message === 'Invalid credentials.'){
                      component.validationErrors = {'password': 'Wrong email or password'};
                      component.$store.commit('ajaxWaiting', false);
                    }
                    let message = 'Internal server error';
                    console.log(message);
                    console.log(error.response);
                    component.$store.commit('ajaxWaiting', false);
                });
            },
            logout: function(event){
                localStorage.removeItem('authToken');
                localStorage.removeItem('refreshToken');
                this.$store.commit('loggedIn', false);
                component.$store.commit('ajaxWaiting', true);
                let component = this;
                axios.create().post(API_URL + '/logout',{}).then(function (response) {
                    if(response.status === 200) {
                        component.$store.commit('loggedIn', false);
                        component.$store.commit('ajaxWaiting', false);
                        component.userEmail = '';
                        component.$store.commit('setData',{});
                    }
                }).catch(function (error) {
                    component.$store.commit('ajaxWaiting', false);
                });
            },
            vkLogin: function (event) {
                event.preventDefault();

                let component = this;
                let body = {};

                component.$store.commit('ajaxWaiting', true);
                axios.create().post(API_URL + '/login-vk', body).then(function (response) {
                    component.$store.commit('loggedIn', true);
                    component.$store.commit('ajaxWaiting', false);
                    component.validationErrors = {};
                    component.userEmail = response.data.user;
                    component.$store.commit('setData',JSON.parse(response.data.userData));
                    const token = response.data.token;
                    localStorage.setItem('authToken', token);
                    const refresh_token = response.data.refresh_token;
                    localStorage.setItem('refreshToken', refresh_token);
                }).catch(function (error) {
                    if(error.response.data.code === 401 && error.response.data.message === 'Invalid credentials.'){
                      component.validationErrors = {'password': 'Wrong email or password'};
                      component.$store.commit('ajaxWaiting', false);
                    }
                    let message = 'Internal server error';
                    console.log(message);
                    console.log(error.response);
                    component.$store.commit('ajaxWaiting', false);
                });
            },
        },
        beforeMount() {
            if (this.authToken) {
                let component = this;
                const axiosConfig = {
                    headers: {
                        'Authorization': 'Bearer ' + this.authToken,
                    }
                }
                component.$store.commit('ajaxWaiting', true);
                this.loaded = false;
                axios.create().post(API_URL + '/userinfo',{},axiosConfig).then(function (response) {
                    if(response.status === 200) {
                        component.$store.commit('loggedIn', true);
                        component.userEmail = response.data.user;
                        component.$store.commit('setData',JSON.parse(response.data.userData));
                        component.$store.commit('ajaxWaiting', false);
                        component.loaded = true;
                    }
                }).catch(function (error) {
                    if (error.response.data.code === 401 && error.response.data.message === 'Expired JWT Token') {
                        component.$store.commit('ajaxWaiting', true);
                        component.$store.commit('loggedIn', false);
                        axios.create().post(API_URL + '/token_refresh', {'refresh_token':component.refreshToken}).then(function (response) {
                            if(response.status === 200){
                                component.validationErrors = {};
                                component.userEmail = response.data.user;
                                component.$store.commit('setData',JSON.parse(response.data.userData));
                                const token = response.data.token;
                                localStorage.setItem('authToken', token);
                                const refresh_token = response.data.refresh_token;
                                localStorage.setItem('refreshToken', refresh_token);
                                component.$store.commit('ajaxWaiting', false);
                                component.$store.commit('loggedIn', true);
                                component.loaded = true;
                            }   
                        }).catch(function (error) {
                            component.$store.commit('ajaxWaiting', false);
                            component.loaded = true;
                        });
                    }
                });
            } else {
                this.$store.commit('ajaxWaiting', false);
            }
        }
    }
</script>

<style>
  .container {
    padding-top: 50px;
  }
</style>
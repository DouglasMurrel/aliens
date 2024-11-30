<template>
  <div v-if="loggedIn">
    <Order />
  </div>

  <h2 v-if="ajaxWaiting">Loading, please wait...</h2>
  <form method="post" v-on:submit.prevent="submitForm" v-if="!loggedIn && loaded">
    <h1>Sign In Form</h1>
    <small class="form-text text-danger" v-if="validationErrors.password">
      {{ validationErrors.password }}
    </small>
    <div class="form-group">
      <label for="email">Email address</label>
      <input type="email" class="form-control" id="email" v-model="email" aria-describedby="emailHelp" placeholder="Enter email">
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <div class="input-group mr-sm-2">
          <input :type="showPass ? 'text' : 'password'" class="form-control" autocomplete="off" id="password" v-model="password" placeholder="Password">
          <div class="input-group-prepend">
              <div class="input-group-text" role="button" @click="showPass=!showPass">
                  <font-awesome-icon v-if="showPass" icon="fa-solid fa-eye" />
                  <font-awesome-icon v-else icon="fa-solid fa-eye-slash" />
              </div>
          </div>
      </div>
    </div>
    <button type="submit" class="btn btn-success">Login</button>
    <div>
      <a :href="vkLoginUrl">or login through VK account</a>
    </div>
  </form>
</template>

<script>
    import axios from 'axios'
    import {API_URL, VK_LOGIN_URL} from '../config.local'
    import Order from '../components/Order.vue'

    export default {
        name: 'SignUpForm',
        data () {
            return {
                msg: 'SignInForm',

                email: '',
                password: '',
                showPass: false,

                validationErrors: {},
                authToken: localStorage.getItem('authToken'),
                refreshToken: localStorage.getItem('refreshToken'),
                userEmail: '',
                loaded: true,
                vkLoginUrl: VK_LOGIN_URL
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
                    component.$store.commit('setHelpers',JSON.parse(response.data.helpers));
                    const token = response.data.token;
                    localStorage.setItem('authToken', token);
                    const refresh_token = response.data.refresh_token;
                    localStorage.setItem('refreshToken', refresh_token);
                    component.password = '';
                }).catch(function (error) {
                    if(error.response.data.code === 401 && error.response.data.message === 'Invalid credentials.'){
                      component.validationErrors = {'password': 'Wrong email or password'};
                      component.$store.commit('ajaxWaiting', false);
                    }
//                    console.log(error.response);
                    component.$store.commit('ajaxWaiting', false);
                    component.password = '';
                });
            },
            vkLogin: function (event) {
                event.preventDefault();

                let component = this;
                let body = {};

                component.$store.commit('ajaxWaiting', true);
                axios.create().post(VK_LOGIN_URL, body).then(function (response) {
                    component.$store.commit('loggedIn', true);
                    component.$store.commit('ajaxWaiting', false);
                    component.validationErrors = {};
                    component.userEmail = response.data.user;
                    component.$store.commit('setData',JSON.parse(response.data.userData));
                    component.$store.commit('setHelpers',JSON.parse(response.data.helpers));
                    const token = response.data.token;
                    localStorage.setItem('authToken', token);
                    const refresh_token = response.data.refresh_token;
                    localStorage.setItem('refreshToken', refresh_token);
                }).catch(function (error) {
                    if(error.response.data.code === 401 && error.response.data.message === 'Invalid credentials.'){
                      component.validationErrors = {'password': 'Wrong email or password'};
                      component.$store.commit('ajaxWaiting', false);
                    }
//                    console.log(error.response);
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
                        component.$store.commit('setHelpers',JSON.parse(response.data.helpers));
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
                                component.$store.commit('setHelpers',JSON.parse(response.data.helpers));
                                const token = response.data.token;
                                localStorage.setItem('authToken', token);
                                const refresh_token = response.data.refresh_token;
                                localStorage.setItem('refreshToken', refresh_token);
                                component.$store.commit('ajaxWaiting', false);
                                component.$store.commit('loggedIn', true);
                                component.loaded = true;
                                window.location.reload();
                            }   
                        }).catch(function (error) {
                            component.$store.commit('ajaxWaiting', false);
                            component.loaded = true;
                        });
                    } else {
                        component.$store.commit('ajaxWaiting', false);
                        component.loaded = true;
                        component.validationErrors = {'password': 'Wrong email or password'};
                    }
                });
            } else {
                this.$store.commit('ajaxWaiting', false);
            }
        },
        components: {
            Order
        }
    }
</script>

<style>
  .container {
    padding-top: 50px;
  }
</style>
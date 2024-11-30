<template>
  <div class="fullHeight" :class="{wait: ajaxWaiting}">
    <div class="container">
      <nav v-if="loggedIn && !ajaxWaiting">
        {{ userData.fullname }}
        <a v-if="loggedIn" href='' @click.prevent="logout">Выйти</a>
      </nav>
      <nav v-else-if="!ajaxWaiting">
        <RouterLink to="/signup" class="me-2">Регистрация</RouterLink>
        <RouterLink to="/signin">Вход</RouterLink>
      </nav>
      <main>
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import {API_URL} from './config.local'

export default {
        name: 'App',
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
            logout: function(event){
                localStorage.removeItem('authToken');
                localStorage.removeItem('refreshToken');
                this.$store.commit('loggedIn', false);
                this.$store.commit('ajaxWaiting', true);
                let component = this;
                axios.create().post(API_URL + '/logout',{}).then(function (response) {
                    if(response.status === 200) {
                        component.$store.commit('loggedIn', false);
                        component.$store.commit('ajaxWaiting', false);
                        component.$store.commit('setData',{});
                    }
                }).catch(function (error) {
                    component.$store.commit('ajaxWaiting', false);
                });
            }
        },
        beforeCreate() {
	   const params = new Proxy(new URLSearchParams(window.location.search), {
              get: (searchParams, prop) => searchParams.get(prop),
	   });
           if (params.token) {
               localStorage.setItem('authToken', params.token)
               localStorage.setItem('refreshToken', params.refreshToken)
               window.location.href = '/'
           }
        }
}
</script>

<style>
 .fullHeight {
    height: 100%;
 }
 .wait {
    cursor: wait;
 }
 .border-bottom-dashed {
    border-bottom: 1px dashed;
    display: inline-block;
 }
</style>
<template>
  <div class="container">
    <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess">
      Ваш пароль был изменен
    </div>

    <form method="post" v-on:submit.prevent="submitForm" v-else>
      <h2 v-if="ajaxWaiting">Loading, please wait...</h2>
      <small class="form-text text-danger" v-if="validationErrors.hasOwnProperty('common') && validationErrors.common">
        {{ validationErrors.common }}
      </small>
      <div class="form-group">
        <label for="password">Пароль</label>
        <input :type="showPass ? 'text' : 'password'" class="form-control" autocomplete="off" id="password" v-model="password" placeholder="Password">
        <a v-html="showPass ? 'Спрятать пароль' : 'Показать пароль'" href="" @click.prevent="showPass=!showPass"></a>
        <small class="form-text text-danger" v-if="validationErrors.password">
          {{ validationErrors.password }}
        </small>
      </div>
      <div class="form-group">
        <label for="password1">Повторите пароль</label>
        <input :type="showPass1 ? 'text' : 'password'" class="form-control" autocomplete="off" id="password1" v-model="password1" placeholder="Repeat password">
        <a v-html="showPass1 ? 'Спрятать пароль' : 'Показать пароль'" href="" @click.prevent="showPass1=!showPass1"></a>
      </div>
      <input type="hidden" v-model="email">
      <input type="hidden" v-model="code">
      <button type="submit" class="btn btn-success">Сохранить</button>
    </form>
  </div>
</template>

<script>
    import axios from 'axios';
    import {API_URL} from '../config.local'

    export default {
        name: 'ConfirmRecoverForm',
        data () {
            return {
                email: this.$route.query.email,
                code: this.$route.query.code,
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
                    email: this.email,
                    code: this.code,
                    password: this.password,
                };

                this.$store.commit('ajaxWaiting', true);
                axios.create().post(API_URL + '/recover-password/check', body).then(function (response) {
                    component.formSubmittedSuccess = true;
                    component.validationErrors = {};
                    component.$store.commit('ajaxWaiting', false);
                }).catch(function (error) {
                    console.log(error.response);
                    component.validationErrors = {'common': error.response.data.error};
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

<template>
  <div class="container">
    <h1>Восстановить пароль</h1>

    <div class="alert alert-success" role="alert" v-if="mailSent && !ajaxWaiting">
      <div>Мы отправили письмо на {{ emailCopy }}. Чтобы задать новый пароль, проверьте почту и пройдите по указанной в письме ссылке</div>
    </div>

    <h2 v-if="ajaxWaiting">Идет обработка, подождите, пожалуйста...</h2>
    <form method="post" v-on:submit.prevent="submitForm">
      <div class="form-group">
        <label for="email">Введите ваш email</label>
        <div class="input-group mb-2">
            <input type="email" class="form-control" id="email" v-model="email" aria-describedby="emailHelp">
        </div>
        <small class="form-text text-danger" v-if="errorText">
          {{ errorText }}
        </small>
      </div>
      <button type="submit" class="btn btn-success">Отправить</button>
    </form>
  </div>
</template>

<script>
    import axios from 'axios'
    import {API_URL} from '../config.local'

    export default {
        name: 'RecoverForm',
        data () {
            return {
                email: '',
                emailCopy: '',
                mailSent: false,
                errorText: ''
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
                    email: this.email
                };

                component.$store.commit('ajaxWaiting', true);
                component.mailSent = false;
                component.errorText = '';
                axios.create().post(API_URL + '/recover-password/create', body).then(function (response) {
                    component.$store.commit('ajaxWaiting', false);
                    component.emailCopy = component.email;
                    component.mailSent = true;
                }).catch(function (error) {
//                    console.log(error.response);
                    component.errorText = error.response.data.error;
                    component.$store.commit('ajaxWaiting', false);
                });
            }
        }
    }
</script>
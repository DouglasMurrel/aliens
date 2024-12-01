<script>
import axios from 'axios'
import {API_URL} from '../config.local'

export default {
    computed: {
        userData () {
            return this.$store.state.userData
        },
        helpers () {
            return this.$store.state.helpers
        },
        loggedIn () {
            return this.$store.state.loggedIn
        },
        ajaxWaiting () {
            return this.$store.state.ajaxWaiting
        }
    },
    data() {
        if (!this.$store.state.userData.userOrder) {
            this.$store.state.userData.userOrder = {'orderCans':[],'orderWants':[],'orderNoes':[]}
        }
        if (this.$store.state.userData.userOrder.school !== 1 && 
            this.$store.state.userData.userOrder.school !== 2 && 
            this.$store.state.userData.userOrder.school !== 3
        ) {
            this.$store.state.userData.userOrder.school = 3
        }
        return {
           userOrder: this.$store.state.userData.userOrder,
           orderCans: this.$store.state.userData.userOrder.orderCans.map((value) => value['id']),
           orderWants: this.$store.state.userData.userOrder.orderWants.map((value) => value['id']),
           orderNoes: this.$store.state.userData.userOrder.orderNoes.map((value) => value['id']),
           wantsHidden: true,
           cansHidden: true,
           noesHidden: true,
           dataSent: false,
           dataError: false,
           dataErrorText: '',
           authToken: localStorage.getItem('authToken'),
           refreshToken: localStorage.getItem('refreshToken'),
        }
    },
    watch: {
        userOrder: {
            handler(newValue) {
                this.$store.state.userData.userOrder = newValue
            },
            deep: 1
        },
        orderCans: {
            handler(newValue) {
                this.$store.state.userData.userOrder.orderCans = [];
                this.helpers.orderCan.forEach((h) => {
                    if (newValue.includes(h.id)) {
                        this.$store.state.userData.userOrder.orderCans.push(h);
                    }
                })
            }
        },
        orderWants: {
            handler(newValue) {
                this.$store.state.userData.userOrder.orderWants = [];
                this.helpers.orderCan.forEach((h) => {
                    if (newValue.includes(h.id)) {
                        this.$store.state.userData.userOrder.orderWants.push(h);
                    }
                })
            }
        },
        orderNoes: {
            handler(newValue) {
                this.$store.state.userData.userOrder.orderNoes = [];
                this.helpers.orderCan.forEach((h) => {
                    if (newValue.includes(h.id)) {
                        this.$store.state.userData.userOrder.orderNoes.push(h);
                    }
                })
            }
        }
    },
    methods: {
        submit: function (event) {
            let component = this;
            let axiosConfig = {
                headers: {
                    'Authorization': 'Bearer ' + this.authToken,
                }
            }
            this.$store.commit('ajaxWaiting', true);
            axios.create().post(API_URL + '/order', this.userOrder, axiosConfig).then(function (response) {
                if(response.status === 200) {
                    component.$store.commit('ajaxWaiting', false);
                    component.dataSent = true;
                    component.dataError = false;
                }
            }).catch(function (error) {
                if (error.response.data.code === 401 && error.response.data.message === 'Expired JWT Token') {
                    component.$store.commit('ajaxWaiting', true);
                    axios.create().post(API_URL + '/token_refresh', {'refresh_token':component.refreshToken}).then(function (response) {
                        if(response.status === 200){
                            const token = response.data.token;
                            component.authToken = token;
                            localStorage.setItem('authToken', token);
                            const refresh_token = response.data.refresh_token;
                            component.resreshToken = refresh_token;
                            localStorage.setItem('refreshToken', refresh_token);

                            let axiosConfig = {
                                headers: {
                                    'Authorization': 'Bearer ' + component.authToken,
                                }
                            }
                            axios.create().post(API_URL + '/order', component.userOrder, axiosConfig).then(function (response) {
                                if(response.status === 200) {
                                    component.$store.commit('ajaxWaiting', false);
                                    component.dataSent = true;
                                    component.dataError = false;
                                }
                            }).catch(function (error) {
                                component.$store.commit('ajaxWaiting', false);
                                component.dataError = true;
                                component.dataErrorText = error;
                                component.dataSent = false;
                            })
                        }   
                    }).catch(function (error) {
                        component.$store.commit('ajaxWaiting', false);
                        component.$store.commit('loggedIn', false);
                        localStorage.removeItem('authToken');
                        localStorage.removeItem('refreshToken');
                        component.dataError = true;
                        component.dataErrorText = error;
                        component.dataSent = false;
                    });
                } else {
                    component.$store.commit('ajaxWaiting', false);
                    component.dataError = true;
                    component.dataErrorText = error;
                }
            })
        }
    }
}
</script>

<template>
    <form @submit.prevent="submit">
        <div class="form-group mb-2">
            <label for="contact">Способы связи (email, ВК, телеграм и т.д.)</label>
            <textarea class="form-control" id="contact" v-model="userOrder.contact"></textarea>
        </div>
        <div class="form-group mb-2">
            <label for="contact">Медицинские противопоказания</label>
            <textarea class="form-control" id="medical" v-model="userOrder.medical"></textarea>
        </div>
        <div class="form-group mb-2">
            <label for="contact">Психологические противопоказания</label>
            <textarea class="form-control" id="psycological" v-model="userOrder.psycological"></textarea>
        </div>
        <div class="form-group mb-2">
            <label for="contact">Пищевые ограничения</label>
            <textarea class="form-control" id="food" v-model="userOrder.food"></textarea>
        </div>

        <div class="mb-2">В какой школе может учиться или преподавать ваш персонаж?
            <div class="form-group">
                <input type="radio" name="school" class="form-check-radio" id="school1" v-model="userOrder.school" value=1>
                <label class="form-check-label ms-2" for="school1">В новой</label>
            </div>
            <div class="form-group">
                <input type="radio" name="school" class="form-check-radio" id="school2" v-model="userOrder.school" value=2>
                <label class="form-check-label ms-2" for="school2">В старой</label>
            </div>
            <div class="form-group">
                <input type="radio" name="school" class="form-check-radio" id="school3" v-model="userOrder.school" value=3>
                <label class="form-check-label ms-2" for="school3">Мне все равно</label>
            </div>
        </div>

        <div>Какие из этих типажей вам хотелось бы сыграть? Можно выбрать несколько
            <span role="button" class="border-bottom-dashed" v-html="wantsHidden?'Показать список типажей':'Спрятать список типажей'" @click="wantsHidden=!wantsHidden"></span>
            <div class="mb-2">(если вам интересны типажи, которых в списке нет, не расстраивайтесь! просто напишите о них в поле ниже)</div>
        </div>
        <div class="form-group mb-2" v-for="item in helpers.orderWant" :key="item.id" v-if="!wantsHidden">
            <input class="form-check-input" type="checkbox" :id="'orderWant' + item.id" v-model="orderWants" :value="item.id" />
            <label class="form-check-label ms-2" :for="'orderWant' + item.id">{{ item.name }}</label>
        </div>
        <div>А какие из этих типажей вы точно НЕ хотите играть? Можно выбрать несколько
                        <span role="button" :class="(noesHidden?'mb-2 ':'') + 'border-bottom-dashed'" v-html="noesHidden?'Показать список типажей':'Спрятать список типажей'" @click="noesHidden=!noesHidden"></span>
        </div>
        <div class="form-group mb-2" v-for="item in helpers.orderNoes" :key="item.id" v-if="!noesHidden">
            <input class="form-check-input" type="checkbox" :id="'orderNoes' + item.id" v-model="orderNoes" :value="item.id" />
            <label class="form-check-label ms-2" :for="'orderNoes' + item.id">{{ item.name }}</label>
        </div>

        <div class="form-group mb-2">
            <label for="contact">А может, вы хотите поиграть во что-то, чего в списке нет? Или у вас есть еще какие-то идеи о персонаже? Тогда напишите об этом!</label>
            <textarea class="form-control" id="comment" v-model="userOrder.comment"></textarea>
        </div>

        <div class="form-group mb-2 mb-0">
            <label for="contact">Что еще вы хотели бы сказать мастерам</label>
            <textarea class="form-control" id="additional" v-model="userOrder.additional" placeholder="Мастера - козлы!"></textarea>
        </div>
    <div class="alert alert-success mt-2" role="alert" v-if="dataSent">
      Ура! Ваша заявка сохранена!<br>
      Скоро с вами свяжутся мастера. А если нет, пишите в личные сообщения <a href="https://vk.com/club228365559" target="_blank">группы игры</a>
    </div>
    <div class="alert alert-danger mt-2 mb-0" role="alert" v-if="dataError">
      Упс:((( Что-то пошло не так<br>
      Расскажите об этом мастерам в личных сообщениях <a href="https://vk.com/club228365559" target="_blank">группы игры</a>.<br>
      И передайте им то, что видите ниже (да, это страшное ругательство на програмистском!):<br>
      {{ dataErrorText }}
    </div>
        <button type="submit" class="btn btn-success">Сохранить заявку!</button>
    </form>
</template>

<script>
import axios from 'axios'
import {API_URL} from '../config.local'

export default {
    computed: {
        isAdmin () {
            return this.$store.state.userData.roles.includes("ROLE_ADMIN")
        }
    },
    data() {
        return {
           authToken: localStorage.getItem('authToken'),
           refreshToken: localStorage.getItem('refreshToken'),
           orders: []
        }
    },
    mounted() {
        if (this.authToken && this.isAdmin) {
            let component = this;
            let axiosConfig = {
                headers: {
                    'Authorization': 'Bearer ' + this.authToken,
                }
            }
            this.$store.commit('ajaxWaiting', true);
            axios.create().post(API_URL + '/admin/all-orders',{},axiosConfig).then(function (response) {
                if(response.status === 200) {
                    component.orders = JSON.parse(response.data);
                    component.$store.commit('ajaxWaiting', false);
                }
            }).catch(function (error) {
                console.log(error);
                component.$store.commit('ajaxWaiting', false);
            });
        } else {
            this.$store.commit('ajaxWaiting', false);
        }
    }
}
</script>
<template>
<div v-if="isAdmin">
    <div v-for="(order,k) in orders" style="border:1px solid;">
        <div>{{ order.fullname }}, {{ order[0].contact }}</div>
        <div>Хочет:
            <div v-for="want in order[0].orderWants" class="ms-3">{{ want.name }}</div>
        </div>
        <div>Не хочет:
            <div v-for="no in order[0].orderNoes" class="ms-3">{{ no.name }}</div>
        </div>
        <div>{{ order[0].comment }}</div>
        <div>{{ order[0].additional }}</div>
        <div style="border-top: 1px dashed;">{{ order[0].psycological }}</div>
    </div>
</div>
</template>

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
    <div v-for="(order,k) in orders">{{ order }}</div>
</div>
</template>

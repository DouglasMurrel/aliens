<script>
import axios from 'axios'
import {API_URL} from '../config.local'

export default {
    data() {
        return {
           authToken: localStorage.getItem('authToken'),
           refreshToken: localStorage.getItem('refreshToken'),
        }
    },
    mounted() {
        if (this.authToken) {
            let component = this;
            let axiosConfig = {
                headers: {
                    'Authorization': 'Bearer ' + this.authToken,
                }
            }
            this.$store.commit('ajaxWaiting', true);
            axios.create().post(API_URL + '/all-orders',{},axiosConfig).then(function (response) {
                if(response.status === 200) {
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
aaaaaaaaaaaaaaa
</template>

<script>
export default {
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
    data() {
        return {
            userOrder: this.$store.state.userData.userOrder? this.$store.state.userData.userOrder : {}
        }
    },
    watch: {
        userOrder: {
            handler(newValue, oldValue) {
                this.$store.state.userData.userOrder = newValue
            },
            deep: true
        }
    },
    methods: {
        submit: function (event) {
            console.log(event);
        }
    }
}
</script>

<template>
    <div>{{ userData }}</div>
    <form @submit.prevent="submit">
        <div class="form-group">
            <label for="contact">Способы связи</label>
            <textarea class="form-control" id="contact" v-model="userOrder.contact"></textarea>
        </div>
        <div class="form-group">
            <label for="contact">Медицинские противопоказания</label>
            <textarea class="form-control" id="medical" v-model="userOrder.medical"></textarea>
        </div>
        <div class="form-group">
            <label for="contact">Психологические противопоказания</label>
            <textarea class="form-control" id="psycological" v-model="userOrder.psycological"></textarea>
        </div>
        <div class="form-group">
            <label for="contact">Пищевые ограничения</label>
            <textarea class="form-control" id="food" v-model="userOrder.food"></textarea>
        </div>


        <div class="form-group">
            <label for="contact">Другие пожелания про персонажа</label>
            <textarea class="form-control" id="comment" v-model="userOrder.comment"></textarea>
        </div>
        <div class="form-group">
            <label for="contact">Что еще вы хотели бы сказать мастерам</label>
            <textarea class="form-control" id="additional" v-model="userOrder.additional" placeholder="Мастера - козлы!"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Сохранить заявку!</button>
    </form>
</template>

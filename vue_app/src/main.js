import { createApp } from 'vue'
import { createStore } from 'vuex'
import router from './router/router'
import App from './App.vue'

const store = createStore({
  state () {
    return {
        userData: {}
    };
  },
  mutations: {
    set (state, object) {
      state.userData = object;
    }
  }
})

createApp(App)
        .use(router)
        .use(store)
        .mount('#vue-app')
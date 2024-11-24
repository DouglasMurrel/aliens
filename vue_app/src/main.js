import { createApp } from 'vue'
import { createStore } from 'vuex'
import router from './router/router'
import App from './App.vue'

const store = createStore({
  state () {
    return {
        userData: {},
        loggedIn: false,
        ajaxWaiting: false
    };
  },
  mutations: {
    setData (state, object) {
      state.userData = object;
    },
    loggedIn (state, flag) {
      state.loggedIn = flag;
    },
    ajaxWaiting (state, flag) {
      state.ajaxWaiting = flag;
    }
  }
});

createApp(App)
        .use(router)
        .use(store)
        .mount('#vue-app')
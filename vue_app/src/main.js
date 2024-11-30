import { createApp } from 'vue'
import { createStore } from 'vuex'
import router from './router/router'

import './scss/styles.scss'
import * as bootstrap from 'bootstrap'
import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import { faEye, faEyeSlash } from '@fortawesome/free-solid-svg-icons'
library.add(faEyeSlash)
library.add(faEye)

import App from './App.vue'

const store = createStore({
  state () {
    return {
        userData: {},
        helpers: {},
        loggedIn: false,
        ajaxWaiting: false
    };
  },
  mutations: {
    setData (state, object) {
      state.userData = object;
    },
    setHelpers (state, object) {
      state.helpers = object;
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
        .component('font-awesome-icon', FontAwesomeIcon)
        .mount('#vue-app')
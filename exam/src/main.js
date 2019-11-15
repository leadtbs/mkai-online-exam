import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'

import './assets/app.scss'
import BootstrapVue from 'bootstrap-vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import { fas } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import '@fortawesome/fontawesome-free/css/all.css'
import '@fortawesome/fontawesome-free/js/all.js'
import VueProgressBar from 'vue-progressbar'

Vue.use(VueProgressBar, {
    color: '#9BD7D1',
    failedColor: 'red',
    thickness: '5px',
})

library.add(fas)
Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.prototype.$axios = axios













require('@/store/subscriber')

axios.defaults.baseURL = 'http://127.0.0.1:8000'

Vue.use(BootstrapVue);

Vue.config.productionTip = false
store.dispatch('auth/attempt', localStorage.getItem('token')).then(() => {
  new Vue({
    router,
    store,
    render: h => h(App)
  }).$mount('#app')
})


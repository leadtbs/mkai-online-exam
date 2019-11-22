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
import Swal from 'sweetalert2'
import PrettyCheckBox from 'pretty-checkbox-vue'
import CircularCountDownTimer from "vue-circular-count-down-timer";

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
})

axios.defaults.baseURL = '/';
Vue.prototype.$axios = axios
Vue.prototype.$Swal = Swal
Vue.prototype.$Toast = Toast

Vue.use(CircularCountDownTimer);
Vue.use(PrettyCheckBox)
Vue.use(VueProgressBar, {
    color: '#9BD7D1',
    failedColor: 'red',
    thickness: '5px',
})

library.add(fas)
Vue.component('font-awesome-icon', FontAwesomeIcon)




require('@/store/subscriber')


Vue.use(BootstrapVue);

Vue.config.productionTip = false
store.dispatch('auth/attempt', localStorage.getItem('token')).then(() => {
  new Vue({
    router,
    store,
    render: h => h(App)
  }).$mount('#app')
})


import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'

// STUDENT SIDE -- START
import Home from '../views/Home.vue'
// STUDENT SIDE -- END

// ADMIN SIDE -- START
import Login from '../views/Admin/Login.vue'
import Admin from '../views/Admin/Admin.vue'
import Sets from '../views/Admin/Sets.vue'
import Questions from '../views/Admin/Questions.vue'
// ADMIN SIDE -- END

Vue.use(VueRouter)

const routes = [
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/admin',
        name: 'admin',
        component: Admin,
        children: [
            {
                path: '/set',
                name: 'set',
                component: Sets,
                children: [
                    {
                        path: ':set_id/questions',
                        name: 'question',
                        component: Questions
                    }
                ]
            }
        ],
        beforeEnter: (to, from, next) => {
        if(!store.getters['auth/authenticated']){
            return next({
                name: 'login'
            })
        }
        
        next()
        }
    },
]

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
})

export default router

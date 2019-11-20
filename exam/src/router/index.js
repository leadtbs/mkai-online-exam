import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'

// STUDENT SIDE -- START
import Home from '../views/Home.vue'
import Quiz from '../views/Quiz.vue'
// STUDENT SIDE -- END

// ADMIN SIDE -- START
import Login from '../views/Admin/Login.vue'
import Admin from '../views/Admin/Admin.vue'
import Sets from '../views/Admin/Sets.vue'
import Questions from '../views/Admin/Questions.vue'
import AddEditQuestions from '../views/Admin/AddEditQuestions.vue'
// ADMIN SIDE -- END

Vue.use(VueRouter)

const routes = [
    {
        path: '/quiz/:set_id',
        name: 'quiz',
        component: Quiz
    },
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
                path: 'set',
                name: 'set',
                component: Sets,
            },
            {
                path: 'set/:set_id/questions',
                name: 'question',
                component: Questions,
                props: true
            },
            {
                path: 'set/:set_id/add-edit-question/:tab_index',
                name: 'add_edit_question',
                component: AddEditQuestions,
                props: true
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

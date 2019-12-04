import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store'

// STUDENT SIDE -- START
import Home from '../views/Home.vue'
import Exam from '../views/Exam.vue'
// STUDENT SIDE -- END

// ADMIN SIDE -- START
import Login from '../views/Admin/Login.vue'
import Admin from '../views/Admin/Admin.vue'
import Sets from '../views/Admin/Sets.vue'
import Questions from '../views/Admin/Questions.vue'
import AddQuestions from '../views/Admin/AddQuestions.vue'
import EditQuestions from '../views/Admin/EditQuestions.vue'
// ADMIN SIDE -- END

Vue.use(VueRouter)

const routes = [
    {
        path: '/exam/:set_id',
        name: 'exam',
        component: Exam
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
                path: 'set/:set_id/add-question/:tab_index',
                name: 'add_question',
                component: AddQuestions,
                props: true
            },
            {
                path: 'set/:set_id/edit-question/:question_id',
                name: 'edit_question',
                component: EditQuestions,
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

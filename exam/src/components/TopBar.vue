<template>
    <div>
        <b-navbar toggleable="lg" type="dark" style="background-color: #00A0EA;">
            <b-navbar-nav><b-nav-item :to="{name: 'mainmenu'}">Home</b-nav-item></b-navbar-nav>
            <b-navbar-brand class="mx-auto" href="#">{{ headTitle }} <b-badge pill variant="success">BETA</b-badge></b-navbar-brand>
            <template v-if="authenticated">      
                <b-navbar-nav><b-nav-item href="#" @click.prevent="signOut">Logout</b-nav-item></b-navbar-nav>       
            </template>
            <template v-else>
                <b-navbar-nav><b-nav-item :to="{name: 'login'}" href="#">Login</b-nav-item></b-navbar-nav>
            </template>
        </b-navbar>
    </div>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex'

    export default {
        data() {
            return {
                headTitle: ''
            }
        },
        computed: {
            ...mapGetters({
                authenticated: 'auth/authenticated',
                user: 'auth/user',
            })
        },
        methods: {
            ...mapActions({
                signOutAction: 'auth/signOut'
            }),
            signOut(){
                this.signOutAction().then(() => {
                    this.$router.replace({
                        name: 'mainmenu'
                    })
                })
            }
        },
        watch: {
            '$route': {
                immediate: true,
                handler(to) {
                    if(to.name == "mainmenu"){
                        this.headTitle = 'MKAI Computer Based Test'
                    }
                    else if(to.name == "jlt_home" || to.name == 'jlt_exam'){
                        this.headTitle = 'MKAI CBT - Japanese Language Test'
                    }
                    else if(to.matched[0].name == 'admin' || to.matched[0].name == 'main_admin'){
                        this.headTitle = 'MKAI CBT - Admin'
                    }
                }
            }
        }
    }
</script>
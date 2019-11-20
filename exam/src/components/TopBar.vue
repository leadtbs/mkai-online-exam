<template>
    <div>
        <b-navbar toggleable="lg" type="dark" style="background-color: #00A0EA;">
            <b-navbar-nav><b-nav-item :to="{name: 'home'}">Home</b-nav-item></b-navbar-nav>
            <b-navbar-brand class="mx-auto" href="#">MKAI Online Exam <b-badge pill variant="success">BETA</b-badge></b-navbar-brand>
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
                        name: 'home'
                    })
                })
            }
        }
    }
</script>
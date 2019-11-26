<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                <div class="card card-signin my-5">
                    <div class="card-body">
                        <h5 class="card-title text-center">MKAI Admin Sign In</h5>
                        <form class="form-signin" @submit.prevent="submit">
                            <div class="form-label-group">
                                <input type="text" id="username" class="form-control" placeholder="Username" v-model="form.username" required autofocus>
                            </div>

                            <div class="form-label-group">
                                <input type="password" id="password" class="form-control" placeholder="Password" v-model="form.password" required>
                            </div>

                            <button class="btn btn-lg btn-primary btn-block text-uppercase" type="submit">Sign in</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapActions } from 'vuex'

    export default {
        name: 'login',
        components: {
            
        },
        data(){
            return {
                form: {
                    username: '',
                    password: ''
                }
            }
        },
        methods: {
            ...mapActions({
                logIn: 'auth/logIn'
            }),
            submit(){
                this.logIn(this.form).then(() => {
                    this.$router.replace({
                        name: 'set'
                    })
                })
            }
        }
    }
</script>

<style scoped>
    .card-signin {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
    }

    .card-signin .card-title {
        margin-bottom: 2rem;
        font-weight: 300;
        font-size: 1.5rem;
    }

    .card-signin .card-body {
        padding: 2rem;
    }

    .form-signin {
        width: 100%;
    }

    .form-signin .btn {
        font-size: 100%;
        border-radius: .5rem;
        letter-spacing: .1rem;
        font-weight: bold;
        transition: all 0.2s;
    }

    .form-label-group {
        position: relative;
        margin-bottom: 1rem;
    }

    .form-label-group input {
        height: 2rem;
    }
</style>
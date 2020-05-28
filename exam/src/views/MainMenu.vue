<template>
    <b-container class="mt-5">
        <b-row class="text-center">
            <b-col lg="4" md="12" sm="12"><b-button variant="success" @click="SET('jlt')">Japanese Language Test</b-button></b-col>
            <b-col lg="4" md="12" sm="12"><b-button variant="primary" @click="SET('nce')">Nursing Care (English)</b-button></b-col>
            <b-col lg="4" md="12" sm="12"><b-button variant="danger" @click="SET('ncj')">Nursing Care (Japanese)</b-button></b-col>
        </b-row>
    </b-container>
</template>

<script>
export default {
    prop: 'set_type',
    name: 'mainmenu',
    data() {
        return {
            sets: {}
        }
    },
    methods: {
        SET(set_type) {
            if(this.$route.name == 'main_admin'){
                this.$router.push({name: 'sets', params: {set_type: set_type}});
            }else{
                this.$router.push({name: 'home', params: {set_type: set_type}});
            }
        }
    },
}
</script>

<style scoped>
    button{
        margin-top: 10px;
        width: 80%;
        height: 200px;
        font-weight: bold;
    }
</style>
<template>
    <div class="card mt-5 justify-content-center">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mt-3 p-0" v-for="(set, index) in sets" :key="index">
                    <table class="text-center table-bordered m-auto" style="width: 95%;">
                        <tr>
                            <td @click="startQuiz(set.id)" style="height: 50px;" colspan="5" class="align-middle font-weight-bold set-pick">{{ set.name }}</td>
                        </tr>
                        <tr>
                            <td class="align-middle" style="width: 20%;"><font-awesome-icon class="fa-fw text-info" icon="clock" />{{ set.time }}</td>
                            <td class="align-middle" style="width: 15%;"><font-awesome-icon class="fa-fw text-success" icon="question" />{{ set.question_count }}</td>
                            <td class="align-middle" style="width: 15%;"><font-awesome-icon class="fa-fw text-success" icon="star" />{{ set.choice_count }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'home',
    data() {
        return {
            sets: {}
        }
    },
    methods: {
        startQuiz(id){
            this.$router.push('/quiz/'+id);
        },
        loadSets(){
            this.$Progress.start();
            this.$axios.get('api/set').then(({data}) => {
                for(let x = 0; x < data.length; x++){
                    let hours = Math.floor((data[x].time / (60 * 60)) % 24);
                    let minutes = Math.floor((data[x].time / 60) % 60);

                    data[x].time = hours + ':' + ((minutes < 10) ? '0' + minutes : minutes);
                }
                this.sets = data;
                this.$Progress.finish();
            })
        }
    },
    created() {
        this.loadSets();
    }
}
</script>

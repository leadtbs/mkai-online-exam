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
                            <td class="align-middle" style="width: 15%;"><font-awesome-icon class="fa-fw text-warning" icon="star" />{{ set.choice_count }}</td>
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
            sets: {},
            set_type: '',
            set_type_name: this.$route.params.set_type,
        }
    },
    methods: {
        startQuiz(id){
            this.$router.push({name: 'exam', params: {set_type: this.set_type_name, set_id: id}});
        },
        loadSets(){
            this.$Progress.start();
            this.$axios.get('api/set', {
                params: {
                    set_type: this.set_type
                }
            }).then(({data}) => {
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
        switch(this.$route.params.set_type){
            case 'jlt': this.set_type = 1; break;
            case 'nce': this.set_type = 2; break;
            case 'ncj': this.set_type = 3; break;
            default: console.log('mao ni');
        }
        let exam = JSON.parse(localStorage.getItem('exam'));
        let name = JSON.parse(localStorage.getItem('name'));
        let set_type_url = JSON.parse(localStorage.getItem('set_type'));
        if(exam){
            this.$Swal.fire({
                title: 'Ongoing Exam ('+exam.name+')',
                text: 'An exam taken by '+name+' is currently active, would you like to continue?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Proceed',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.value) {
                    this.$router.push({name: 'exam', params: {set_type: set_type_url, set_id: exam.id}});
                }
                else{
                    localStorage.clear();
                    this.loadSets();
                }
            })
        }
        else{
            this.loadSets();
        }
    }
}
</script>

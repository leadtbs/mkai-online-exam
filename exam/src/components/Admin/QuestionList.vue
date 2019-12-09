<template>
    <div class="container">
        <div class="row">
            <div class="col-md-4 mt-2 p-0" v-for="(question, index) in questions" :key="index">
                <table class="text-center table-bordered m-auto" style="width: 95%">
                    <tr>
                        <td style="height: 100px;" colspan="3" class="align-middle font-weight-bold">
                            <img :src="$URL+'/img/question/'+question.picture" alt="Question" style="width: 100%;">
                        </td>
                    </tr>
                    <tr>
                        <td class="align-middle" style="width: 15%"><font-awesome-icon class="fa-fw text-success" icon="star" />{{ question.choice_count }}</td>
                        <td class="align-middle" style="width: 10%">
                            <button class="btn btn-xs btn-primary" @click="editQuestion(question.id)"><font-awesome-icon icon="edit" /></button>
                        </td>
                        <td class="align-middle" style="width: 10%">
                            <button class="btn btn-xs btn-danger" @click="deleteQuestion(question.id)"><font-awesome-icon icon="trash" /></button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'set_id', 'tab_index'
    ],
    data() {
        return {
            questions: []
        }
    },
    methods: {
        editQuestion(question_id){
            this.$router.push({ name: 'edit_question', params: {question_id} });
        },
        deleteQuestion(question_id){
            this.$Swal.fire({
                title: 'Are you sure?',
                text: '',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if(result.value){
                    this.$Progress.start();
                    this.$axios.delete('api/delete-question/'+question_id)
                    .then(() => {
                        this.loadQuestion();
                    })
                    .catch(() => {
                        this.$Swal.fire('Failed!', 'There was something wrong', 'warning');
                    })
                }
            })
        },
        loadQuestion(){
            this.$axios.get('api/questions/'+this.set_id+'/tab/'+this.tab_index)
            .then(({data}) => {
                this.questions = data;
                this.$Progress.finish();
            })
        }
    },
    mounted() {
        this.$Progress.start();
        this.loadQuestion();
    }
}
</script>
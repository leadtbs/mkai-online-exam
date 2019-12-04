<template>
    <div>
        <QuestionCardHeader 
            :tab_index="$route.params.tab_index"
            editCounter="true"
        />

        <div class="card-body" style="min-height: 400px;">
            <form @submit.prevent="submitQuestion()" action="post" enctype="multipart/form-data">
                <div class="row">
                    <!-- LEFT -->

                    <div class="col-md-5">
                        <div class="custom-file col-md-12 mb-1">
                            <div class="row">
                                <div class="col-md-9">
                                    <input type="file" class="custom-file-input" @change="audioFileChange($event)">
                                    <label class="custom-file-label" for="customFile"></label>
                                </div>
                                <div class="col-md-3">
                                    <button v-if="question" :disabled="playing" @click="playAudio(question.audio)" class="btn btn-md btn-success"><i class="fa fa-play"></i></button>&nbsp;
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</template>

<script>
import QuestionCardHeader from '@/components/Admin/QuestionCardHeader'
var audio

export default {
    name: 'editQuestion',
    data() {
        return {
            question: null,
            playing: false,
            audioName: null,
            imgName: null
        }
    },
    methods: {
        submitQuestion(){
            
        },
        playAudio(audioFile){
            audio = new Audio(this.$URL+'/audio/'+audioFile);
            audio.play();
            this.playing = true;
            audio.onended = () => {
                this.playing = false;
                audio.pause();
                audio.currentTime = 0;
            }
        },
        audioFileChange(event){
            this.question.audio = event.target.files[0];
            this.audioName = event.target.files[0].name;
        }
    },
    created() {
        this.$axios.get('api/get-questions-and-choices/'+this.$route.params.question_id)
        .then(({data}) => {
            this.question = data;
            this.question.audioChanged = false;
            this.question.imgChanged = false;
        })
    },
    components: {
        QuestionCardHeader
    }
}
</script>
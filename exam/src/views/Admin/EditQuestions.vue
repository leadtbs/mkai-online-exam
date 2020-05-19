<template>
    <div>
        <QuestionCardHeader 
            :tab_index="$route.params.tab_index"
            editCounter="true"
        />

        <div class="card-body" style="min-height: 400px;" v-if="question">
            <form @submit.prevent="updateQuestion()" action="post" enctype="multipart/form-data">
                <div class="row">
                    <!-- LEFT -->
                    <div class="col-md-5">
                        <div class="custom-file col-md-12 mb-3" v-if="ifJLT">
                            <div class="row mb-2">
                                <div class="col-md-10">
                                    <input type="file" class="custom-file-input" @change="audioFileChange($event)">
                                    <label class="custom-file-label" for="customFile">{{ audioName }}</label>
                                </div>
                                <div class="col-md-2">
                                    <button v-if="question.audio" :disabled="playing" @click="playAudio(question.audio)" class="btn btn-md btn-success"><i class="fa fa-play"></i></button>&nbsp;
                                </div>
                            </div>
                        </div>
                        
                        <div class="custom-file col-md-12">
                            <input type="file" class="custom-file-input" @change="imgFileChange">
                            <label class="custom-file-label" for="customFile">{{ imgName }}</label>
                        </div>
                        <div id="preview" class="col-md-12 mb-5 mt-2">
                            <img :src="showImage" />
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                    
                    <div class="col-md-1" style="width: 55%;"></div>

                    <!-- RIGHT -->
                    <div class="col-md-6 overflow-auto" style="max-height: 400px;">
                        
                        <div  class="row clearfix" :class="{ 'mt-4' : index != 0}" v-for="(cs, index) in question.choice_set" :key="index">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-11 col-sm-11 col-xs-11">
                                            <input type="text" v-model="cs.description" class="form-control" placeholder="Description / Additional Question (Optional)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <template v-if="question.choice_type == false">
                                        <div class="form-group">
                                            <div class="row" v-for="index2 in 4" :key="index2">
                                                <p-radio :name="'choice_' + index" v-model="cs.correct" v-bind:value="index2-1"
                                                    class="p-icon p-round p-jelly mt-2 ml-3 mr-0 pull-left col-md-1 col-sm-1" 
                                                    color="success" style="font-size: 20px; position: relative; top: 10px;">

                                                    <i slot="extra" class="icon fa fa-check text-white"></i>
                                                </p-radio>
                                                <div class="col-md-8 col-sm-8">
                                                    <input v-model="cs.choices[index2-1].choices" class="form-control" :placeholder="'Choice ' + index2">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="form-group" v-for="index2 in 2" :key="index2">
                                            <div class="row">
                                                <p-radio :name="'choice_' + index" v-model="cs.correct" v-bind:value="(index2 == 1) ? index2-1 : index2"
                                                    class="p-icon p-round p-jelly col-md-1 col-sm-1"
                                                    color="success" style="font-size: 25px; margin-top: 40px;">

                                                    <i slot="extra" class="icon fa fa-check text-white"></i>
                                                </p-radio>
                                                <div class="col-md-3 col-sm-3" style="height: 80px; border: 1px solid;" @click="clickInput(index, (index2 == 1) ? index2-1 : index2)">
                                                    
                                                    <img 
                                                        :src="imgChoice(index, (index2 == 1) ? index2-1 : index2)" 
                                                        style="width: 100%; height: 100%;" />
                                                    <div class="custom-file col-md-12" style="display: none;">
                                                        <input :class="'choice_' + index" type="file" class="custom-file-input" @change="choiceFileChange($event, index, (index2 == 1) ? index2-1 : index2)">
                                                    </div>
                                                </div>

                                                <div class="col-md-1"></div>

                                                <p-radio :name="'choice_' + index" v-model="cs.correct" v-bind:value="(index2 == 1) ? index2 : index2+1"
                                                    class="p-icon p-round p-jelly col-md-1 col-sm-1"
                                                    color="success" style="font-size: 25px; margin-top: 40px;">

                                                    <i slot="extra" class="icon fa fa-check text-white"></i>
                                                </p-radio>
                                                <div class="col-md-3 col-sm-3" style="height: 80px; border: 1px solid;" @click="clickInput(index, (index2 == 1) ? index2 : index2+1)">
                                                    <img 
                                                        :src="imgChoice(index, (index2 == 1) ? index2 : index2+1)" 
                                                        style="width: 100%; height: 100%;" />
                                                    <div class="custom-file col-md-12" style="display: none;">
                                                        <input :class="'choice_' + index" type="file" class="custom-file-input" @change="choiceFileChange($event, index, (index2 == 1) ? index2 : index2+1)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
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
            audioName: '',
            imgName: '',
            img: '',
            ifJLT: (this.$route.params.set_type == 'jlt') ? true : false
        }
    },
    methods: {
        updateQuestion(){
            this.$Progress.start();
            let formData = new FormData();
            formData.append('image', this.question.img_url);
            formData.append('audio', this.question.audio_url);

            if(this.question.choice_type == true){
                for(let x = 0; x < this.question.choice_set.length; x++){
                    let z = 0;
                    for(let y = 0; y < this.question.choice_set[x].choices.length; y++){
                        let choice = this.question.choice_set[x].choices[y];
                        if(choice.choice_url){
                            formData.append('choices['+x+']['+z+']', choice.choice_url);
                            formData.append('choice_id['+x+']['+z+']', choice.id);
                            z++;
                            choice.choices = '';
                        }
                    }
                }
            }

            formData.append('data', JSON.stringify(this.question));

            this.$axios.post('api/update-question', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                params: {
                    set_type: this.$route.params.set_type
                }
            })
            .then(() => {
                this.initQuestion();
                this.$Progress.finish();
            })
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
        clickInput(index, choice){
            document.getElementsByClassName("choice_"+index)[choice].click();
        },
        audioFileChange(event){
            this.question.audio_url = event.target.files[0];
            this.audioName = event.target.files[0].name;
        },
        imgFileChange(){
            this.question.img_url = event.target.files[0];
            let input = event.target;
            if(input.files && input.files[0]){
                let reader = new FileReader();
                reader.onload = (e) => {
                    this.img = e.target.result;
                }

                this.imgName = input.files[0].name;
                reader.readAsDataURL(input.files[0]);
            }
        },
        choiceFileChange: function(event, index, choice_index){
            this.question.choice_set[index].choices[choice_index].choice_url = event.target.files[0];
            let input = event.target;
            if(input.files && input.files[0]){
                let reader = new FileReader();
                reader.onload = (e) => {
                    this.question.choice_set[index].choices[choice_index].choices = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        },
        imgChoice(index, choice_index){
            if(this.question.choice_set[index].choices[choice_index].choice_url){
                return this.question.choice_set[index].choices[choice_index].choices;
            }else{
                let c = (this.ifJLT) ? 'choices' : 'ncchoices';
                return this.$URL+'/img/'+c+'/'+this.question.choice_set[index].choices[choice_index].choices;
            }
        },
        initQuestion(){
            this.$axios.get('api/get-questions-and-choices/'+this.$route.params.question_id, {
                params: {
                    set_type: this.$route.params.set_type
                }
            })
            .then(({data}) => {
                this.question = data;
                this.question.img_url = '';
                this.question.audio_url = '';
                this.playing = false;
                this.audioName = '';
                this.imgName = '';
                this.img = '';
            })
        }
    },
    computed: {
        showImage: function () {
            let q = (this.ifJLT) ? 'question' : 'ncquestion';
            return (this.img) ? this.img : this.$URL+'/img/'+q+'/'+this.question.picture;
        }
    },
    created() {
        this.initQuestion();
    },
    components: {
        QuestionCardHeader
    }
}
</script>

<style scoped>
    #preview {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #preview img {
        max-width: 100%;
        max-height: 500px;
    }
</style>
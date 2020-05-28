<template>
    <div>
        <QuestionCardHeader 
            :tab_index="$route.params.tab_index"
            v-on:triggerChoiceTypeChange="choiceTypeChange"
            v-on:triggerAddChoiceForm="addChoiceForm"
            addCounter="add"
            :ifJLT="ifJLT"
        />

        <div class="card-body" style="min-height: 400px;">
            <form @submit.prevent="submitQuestion()" method="post" enctype="multipart/form-data">
                <div class="row">
                    <!-- LEFT -->
                    <div class="col-md-5">
                        <div class="custom-file col-md-12 mb-1" v-if="ifJLT">
                            <input type="file" class="custom-file-input" @change="audioFileChange($event)">
                            <label class="custom-file-label" for="customFile">{{ audioName }}</label>
                        </div>
                        <div class="custom-file col-md-12">
                            <input type="file" class="custom-file-input" @change="imgFileChange" required>
                            <label class="custom-file-label" for="customFile">{{ imgName }}</label>
                        </div>
                        <div id="preview" class="col-md-12 mb-5 mt-2">
                            <img v-if="img" :src="img" />
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                    <div class="col-md-1" style="width: 55%;"></div>
                    <!-- RIGHT -->
                    <div class="col-md-6 overflow-auto" style="max-height: 400px;">
                        
                        <div class="row clearfix" :class="{ 'mt-4' : index != 0 }" v-for="(form,index) in upload.forms" :key="index">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-11 col-sm-11 col-xs-11">
                                            <input type="text" v-model="form.description" class="form-control" placeholder="Description / Additional Question (Optional)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <template v-if="upload.choice_type == false">
                                        <div class="form-group">
                                            <div class="row" v-for="index2 in 4" :key="index2">
                                                <p-radio 
                                                    :name="'choice_' + index" v-model="form.correct" :value="index2-1" 
                                                    class="p-icon p-round p-jelly mt-2 ml-3 mr-0 pull-left col-md-1 col-sm-1" 
                                                    color="success" style="font-size: 20px; position: relative; top: 10px;">

                                                    <i slot="extra" class="icon fa fa-check text-white"></i>
                                                </p-radio>
                                                <div class="col-md-8 col-sm-8">
                                                    <input v-model="form.choices[index2-1].choice" class="form-control" :placeholder="'Choice ' + index2">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="form-group" v-for="index2 in 2" :key="index2">
                                            <div class="row">
                                                <p-radio :name="'choice_' + index" v-model="form.correct" v-bind:value="(index2 == 1) ? index2-1 : index2"
                                                    class="p-icon p-round p-jelly col-md-1 col-sm-1"
                                                    color="success" style="font-size: 25px; margin-top: 40px;">

                                                    <i slot="extra" class="icon fa fa-check text-white"></i>
                                                </p-radio>
                                                <div class="col-md-3 col-sm-3" style="height: 80px; border: 1px solid;" @click="clickInput(index, (index2 == 1) ? index2-1 : index2)">
                                                    <img 
                                                        v-if="form.choices[(index2 == 1) ? index2-1 : index2].choice" 
                                                        :src="form.choices[(index2 == 1) ? index2-1 : index2].choice" style="width: 100%; height: 100%;" />
                                                    <div class="custom-file col-md-12" style="display: none;">
                                                        <input :class="'choice_' + index" type="file" class="custom-file-input" @change="choiceFileChange($event, index, (index2 == 1) ? index2-1 : index2)">
                                                    </div>
                                                </div>

                                                <div class="col-md-1"></div>

                                                <p-radio :name="'choice_' + index" v-model="form.correct" v-bind:value="(index2 == 1) ? index2 : index2+1"
                                                    class="p-icon p-round p-jelly col-md-1 col-sm-1"
                                                    color="success" style="font-size: 25px; margin-top: 40px;">

                                                    <i slot="extra" class="icon fa fa-check text-white"></i>
                                                </p-radio>
                                                <div class="col-md-3 col-sm-3" style="height: 80px; border: 1px solid;" @click="clickInput(index, (index2 == 1) ? index2 : index2+1)">
                                                    <img 
                                                        v-if="form.choices[(index2 == 1) ? index2 : index2+1].choice" 
                                                        :src="form.choices[(index2 == 1) ? index2 : index2+1].choice" style="width: 100%; height: 100%;" />
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

export default {
    name: 'addQuestion',
    data() {
        return {
            imgName: 'Choose Image',
            audioName: 'Choose Audio',
            img: '',
            img_url: '',
            audio_url: '',
            upload: {
                set_id: parseInt(this.$route.params.set_id),
                section_id: parseInt(this.$route.params.tab_index),
                choice_type: false,
                forms: []
            },
            ifJLT: (this.$route.params.set_type == 'jlt') ? true : false
        }
    },
    methods: {
        clickInput(index, choice){
            document.getElementsByClassName("choice_"+index)[choice].click();
        },
        choiceFileChange: function(event, index, choice_index){
            this.upload.forms[index].choices[choice_index].choice_url = event.target.files[0];
            let input = event.target;
            if(input.files && input.files[0]){
                let reader = new FileReader();
                reader.onload = (e) => {
                    this.upload.forms[index].choices[choice_index].choice = e.target.result;
                }

                reader.readAsDataURL(input.files[0]);
            }
        },
        submitQuestion(){
            this.$Progress.start();
            let formData = new FormData();
            formData.append('image', this.img_url);
            formData.append('audio', this.audio_url);
            formData.append('set_type', this.$route.params.set_type);
            if(this.upload.choice_type == true){
                for(let x = 0; x < this.upload.forms.length; x++){
                    for(let y = 0; y < this.upload.forms[x].choices.length; y++){
                        formData.append('choices['+x+']['+y+']', this.upload.forms[x].choices[y].choice_url);
                        this.upload.forms[x].choices[y].choice = '';
                    }
                }
            }
            formData.append('data', JSON.stringify(this.upload));

            this.$axios.post('api/submit', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
                params: {
                    params: {
                        set_type: this.$route.params.set_type
                    }
                }
            })
            .then(() => {
                this.clearAll();
                this.$Toast.fire({
                    icon: 'success',
                    title: 'Data added successfuly'
                })
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
            })
        },
        imgFileChange: function(event){
            this.img_url = event.target.files[0];
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
        audioFileChange(event){
            this.audio_url = event.target.files[0];
            this.audioName = event.target.files[0].name;
        },
        choiceTypeChange(value){
            for(let x = 0; x < this.upload.forms.length; x++){
                for(let y= 0; y < this.upload.forms[x].choices.length; y++){
                    this.upload.forms[x].choices[y].choice = '';
                    this.upload.forms[x].choices[y].choice_url = '';
                }
            }
            this.upload.choice_type = value;
        },
        addChoiceForm(){
            this.generateChoices();
        },
        generateChoices(){
            this.upload.forms.push({
                choice_set_id: '',
                question_id: '',
                description: '',
                correct: '',
                choices: [
                    { choice: '', choice_url: '' },
                    { choice: '', choice_url: '' },
                    { choice: '', choice_url: '' },
                    { choice: '', choice_url: '' },
                ]
            });
        },
        clearAll(){
            this.upload.forms = [];
            this.imgName = 'Choose Image';
            this.audioName = 'Choose Audio';
            this.img = '';
            this.img_url = '';
            this.audio_url = '';
            this.generateChoices();
        }
    },
    created() {
        this.generateChoices();
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
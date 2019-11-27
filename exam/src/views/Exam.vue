<template>
    <div class="col-md-11 m-auto">
        <div class="card mt-5 justify-content-center">
            <div class="card-body">
                <div class="row">
                    <div v-if="exam" class="col-md-3">
                        <div class="row">
                            <div class="mx-auto pl-3">
                                <circular-count-down-timer style="text-align: center;"
                                    :initial-value="exam.time"
                                    :size="80"
                                    :hour-label="''"
                                    :minute-label="''"
                                    :second-label="''"
                                    @finish="submitExam"
                                ></circular-count-down-timer>
                            </div>
                        </div>

                        <div class="row mt-3 text-center" v-if="exam">
                            <div class="col-md-12 font-weight-bold" :class="sectionColor">
                                {{ exam.section[current_section].name }}
                            </div>
                            <div class="col-md-12">
                                <b-progress striped :max="max">
                                    <b-progress-bar
                                        v-for="(sections, index) in exam.section"
                                        :key="index"
                                        :variant="variant[index]"
                                        :value="(progress[index].bar / progress[index].max) * 100">
                                    </b-progress-bar>
                                </b-progress>
                            </div>
                        </div>

                        <div class="row mt-5 mb-2" v-if="exam">
                            <div class="col-md-9 text-center m-auto">
                                <button
                                    v-for="(question, index) in exam.section[current_section].question"
                                    :key="index"
                                    :class="[dottedProgress(index), dottedPicked(index)]"
                                    @click="pickQuestion(index)"
                                    class="rounded-circle btn btn-sm ml-1"
                                    style="width: 5px; height: 18px;"></button>
                            </div>
                        </div>

                        <div class="row mt-5" v-if="exam.section[current_section].question[current].audio">
                            <div class="col-md-12 text-center" style="display: inline-block;">

                                <button :disabled="playing" @click="playAudio(exam.section[current_section].question[current].audio)" class="btn btn-md btn-success"><i class="fa fa-play"></i></button>&nbsp;
                                    <font-awesome-icon 
                                        v-for="index in 2"
                                        :key="index"
                                        icon="heart" 
                                        class="align-middle fa-2x"
                                        :class="(exam.section[current_section].question[current].audio_counter >= index) ? 'text-danger' : 'text-secondary'"
                                    />
                        
                            </div>
                        </div>

                        <div class="row mt-5 mb-2">
                            <div class="col-md-12 text-center">
                                <button :disabled="submitDisabled" @click="(submit) ? submitButton() : nextSection()" class="btn btn-md btn-danger">{{ (submit) ? 'Submit' : 'Next Section' }}</button>&nbsp;
                                <button :disabled="submitDisabled" @click="nextQuestion()" class="btn btn-md btn-success">Next Question</button>
                            </div>
                        </div>
                    </div>
                    <div v-if="exam" class="col-md-6">
                        <img :src="$URL+'/img/question/'+exam.section[current_section].question[current].picture" alt="question" class="w-100 border">
                    </div>
                    <div v-if="exam" class="col-md-3 overflow-auto" style="font-size: 24px; min-height: 100px; max-height: 400px;">
                        <div v-for="(choice_set, index) in exam.section[current_section].question[current].choice_set" :key="index" class="row border border-dark mb-2 pb-2">
                            <div class="col-md-12 font-weight-bold">
                                {{ choice_set.description }}
                            </div>
                            <div v-if="exam.section[current_section].question[current].choice_type == true" class="col-md-12">
                                <div class="row">
                                    <div v-for="(choices, index2) in choice_set.choices" :key="index2" class="col-6 col-sm-6 col-md-6 p-1">
                                        <div @click="pick(index, index2)" v-bind:value="index2" class="border mt-3 text-wrap clickable" v-if="choices.choices != ''">
                                            <img :src="$URL+'/img/choices/'+choices.choices" alt="choice" class="w-100 h-100" :class="ifPicked(index, index2)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="exam.section[current_section].question[current].choice_type == false" class="col-md-12">
                                <div v-for="(choices, index2) in choice_set.choices" :key="index2" class="p-0">
                                    <div @click="pick(index, index2)" v-bind:value="index2" class="border mt-2 text-wrap clickable" v-if="choices.choices != ''" :class="ifPicked(index, index2)">
                                        {{ choices.choices }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import jsPDF from 'jspdf'
import 'jspdf-autotable'
var audio

export default {
    name: 'exam',
    data() {
        return {
            variant: ['info', 'danger', 'primary', 'success'],
            max: 0,
            progress: [
            ],
            playing: false,
            submit: false,
            submitDisabled: false,
            current_section: 0,
            current: 0,
            exam: null,
        }
    },
    methods: {
        pick(choice_set_index, choice_index){
            this.exam.section[this.current_section].question[this.current].choice_set[choice_set_index].picked = choice_index;
        },
        shuffle(array){
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }

            return array;
        },
        startExam(){
            this.$Swal.fire({
                title: 'Enter Set Password',
                input: 'password',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
            }).then((result) => {
                if (result.value) {
                    this.$axios.post('/api/confirm_password', {
                        password: result.value,
                        id: this.$route.params.set_id
                    })
                    .then(({data}) => {
                        if(data !== 'wrong'){
                            this.max = data.section.length * 100;
                            for(let x = 0; x < data.section.length; x++){

                                this.progress.push({bar: 0, max: data.section[x].question.length});
                                data.section[x].question = this.shuffle(data.section[x].question);

                                for(let y = 0; y < data.section[x].question.length; y++){

                                    const preload_image = new Image();
                                    preload_image.src = this.$URL+'/img/question/'+data.section[x].question[y].picture;

                                    if(data.section[x].question[y].audio){
                                        data.section[x].question[y].audio_counter = 2;
                                    }

                                    for(let z = 0; z < data.section[x].question[y].choice_set.length; z++){
                                        data.section[x].question[y].choice_set[z].picked = null;
                                        data.section[x].question[y].choice_set[z].choices = this.shuffle(data.section[x].question[y].choice_set[z].choices);
                                    }
                                }
                            }
                            this.exam = data;
                        }
                        else{
                            this.$Swal.fire({
                                type: 'error',
                                title: 'Wrong Password',
                                text: 'Try again or call attention of sensei',
                            }).then(() => {
                                this.startExam();
                            })
                        }
                    })
                }
            })
        },
        ifPicked(index, index2){
            return (index2 == this.exam.section[this.current_section].question[this.current].choice_set[index].picked ? 'picked' : '');
        },
        dottedProgress(index){
            let count = 0;
            let section = this.exam.section[this.current_section];
            let choice_set = section.question[index].choice_set;
            let picked = true;

            for(let x = 0; x < section.question.length; x++){
                let counter = true;
                for(let y = 0; y < section.question[x].choice_set.length; y++){
                    if(section.question[x].choice_set[y].picked == null){
                        counter = false;
                        break;
                    }
                }
                if(counter == true){
                    count++;
                }
            }
            
            this.progress[this.current_section].bar = count;

            for(let x = 0; x < choice_set.length; x++){
                if(choice_set[x].picked == null){
                    picked = false;
                    break;
                }
            }

            this.progressBar;
            return (picked) ? 'bg-success' : 'bg-primary';
        },
        dottedPicked(index){
            return (this.current == index) ? 'btn-outline-danger' : '';
        },
        playAudio(audioFile){
            if(this.exam.section[this.current_section].question[this.current].audio_counter != 0){
                this.exam.section[this.current_section].question[this.current].audio_counter--;
                this.playing = true;
                audio = new Audio(this.$URL+'/audio/'+audioFile);
                audio.play();
                audio.onended = () => {
                    if(this.exam.section[this.current_section].question[this.current].audio_counter != 0){
                        this.playing = false;
                    }
                }
            }
        },
        resetAudio(){
            if(audio){
                if(this.exam.section[this.current_section].question[this.current].audio_counter != 0){
                    this.playing = false
                }else{
                    this.playing = true
                }
                audio.pause()
                audio.currentTime = 0
            }
        },
        pickQuestion(index){
            this.current = index
            this.resetAudio()
        },
        nextSection(){
            this.$Swal.fire({
                title: 'Proceed to next Section?',
                text: "You won't be able to go back to this section",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Proceed'
            }).then((result) => {
                if (result.value) {
                    this.resetAudio()
                    this.current = 0;
                    this.current_section++
                    if(this.current_section == this.exam.section.length-1){
                        this.submit = true
                    }
                }
            })
        },
        nextQuestion(){
            if(this.current < this.exam.section[this.current_section].question.length-1){
                this.$Progress.start();
                this.current++;
                this.$Progress.finish();
            }
            else if(this.current_section <= this.exam.section.length-1){
                this.$Progress.start();
                this.current = 0;
                this.$Progress.finish();
            }

            if(audio){
                if(this.exam.section[this.current_section].question[this.current].audio_counter != 0){
                    this.playing = false;
                }else{
                    this.playing = true;
                }
                audio.pause();
                audio.currentTime = 0;
            }
        },
        submitButton(){
            this.$Swal.fire({
                title: 'Submit?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Proceed'
            }).then((result) => {
                if (result.value) {
                    this.submitExam();
                }
            });
        },
        submitExam(){
            this.progress[this.current_section].bar++;
            this.submitDisabled = true;
            this.$axios.post('/api/submit_exam', this.exam)
            .then(({data}) => {
                let columns = [
                    {title: 'Section', dataKey: 'Section'},
                    {title: 'Score', dataKey: 'Score'},
                    {title: 'Percent', dataKey: 'Percent'},
                ];

                let rows = [];

                for(let x = 0; x < data.scores.length; x++){
                    rows.push([]);
                    rows[x].push(data.scores[x].section)
                    rows[x].push(data.scores[x].score)
                    rows[x].push(data.scores[x].percent)
                }
                
                let doc = new jsPDF('p', 'pt');

                doc.text(40, 30, 'Set Name: ' + data.set_name)

                doc.autoTable({
                    head: [columns],
                    body: rows,

                })
                
                doc.save('Result.pdf');

                setTimeout(() => {
                    this.$router.push('/');
                }, 5000)
            })
        }
    },
    computed: {
        sectionColor() {
            if(this.current_section == 0){
                return 'text-info';
            }
            else if(this.current_section == 1){
                return 'text-danger';
            }
            else if(this.current_section == 2){
                return 'text-primary';
            }
            else if(this.current_section == 3){
                return 'text-success';
            }
            return '';
        },
    },
    created() {
        this.startExam();
    }
}
</script>

<style scoped>
    .picked {
        outline-style: solid;
        outline-color: blue;
    }
    .clickable {
        cursor: pointer;
    }
</style>
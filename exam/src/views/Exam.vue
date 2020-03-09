<template>
    <div class="card mt-5 justify-content-center">
        <div class="card-body">
            <div class="row">
                <div v-if="examStart" class="col-md-3">
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

                    <div class="row mt-3 text-center" v-if="examStart">
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

                    <div class="row mt-5 mb-2" v-if="examStart">
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

                <div v-if="examStart" class="col-md-6">
                    <img :src="$URL+'/img/question/'+exam.section[current_section].question[current].picture" alt="question" class="w-100 border">
                </div>
                <div v-if="examStart" class="col-md-3 overflow-auto" style="font-size: 24px; min-height: 100px; max-height: 400px;">
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

        <b-modal centered no-close-on-backdrop no-close-on-esc hide-footer
            id="password-modal"
            ref="password-modal"
            title="Enter Info"
            ok-title="Submit"
            @ok="startExam">
            <b-form @submit.prevent="startExam">

                <b-form-group>
                    <div class="camera" v-show="!picTaken">
                        <video autoplay id="feed"></video>
                    </div>
                    
                    <div class="picture" v-show="picTaken">
                        <canvas id="canvas"></canvas>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-success mt-1" @click="takePicture">Take Photo</button>
                    </div>
                    
                </b-form-group>

                <b-form-group>
                    <b-form-input
                        autocomplete="off" 
                        type="text"
                        required
                        placeholder="Enter Your Name"
                        v-model="form.name"
                    >
                    </b-form-input>
                </b-form-group>

                <b-form-group>
                    <b-form-input
                        autocomplete="off" 
                        placeholder="Enter Your Teacher's Name"
                        required
                        v-model="form.sensei"
                    >
                    </b-form-input>
                </b-form-group>

                <b-form-group>
                    <b-form-input
                        autocomplete="off" 
                        type="password"
                        placeholder="Enter Set Password"
                        required
                        v-model="form.password"
                    >
                    </b-form-input>
                </b-form-group>

                <b-form-group>
                    <b-button type="submit" :disabled="submitInfo" variant="primary">Submit</b-button>
                </b-form-group>

            </b-form>
        </b-modal>

        <b-modal centered no-close-on-backdrop no-close-on-esc
            id="loading-modal"
            ref="loading-modal"
            title="Loading Assets... Please Wait "
            hide-footer>
            <b-progress :max="totalAssets" animated>
                <b-progress-bar :value="assetsLoaded" :label="`${Math.round((assetsLoaded/totalAssets) * 100)}%`"></b-progress-bar>
            </b-progress>
        </b-modal>
    </div>
</template>

<script>
import { isIOS } from 'mobile-device-detect';
import jsPDF from 'jspdf'
import 'jspdf-autotable'
var audio

export default {
    name: 'exam',
    data() {
        return {
            form: {
                name: '',
                sensei: '',
                password: ''
            },
            variant: ['info', 'danger', 'primary', 'success'],
            max: 0,
            progress: [
            ],
            playing: false,
            submit: false,
            submitDisabled: false,
            current_section: 0,
            current: 0,
            totalAssets: 0,
            assetsLoaded: 0,
            allLoaded: false,
            examStart: false,
            exam: null,
            submitInfo: false,
            timeSpent: [0, 0, 0, 0],
            iOS: isIOS ? true : false,
            picture: null,
            picTaken: false,
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
        initExam(){
            this.$refs['password-modal'].show();
            this.initCamera();
        },
        initCamera(){
            if('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices){
                
                    let constraints = {
                        video: {
                            width: {
                                min: 640,
                                ideal: 768,
                                //max: 1920
                            },
                            height: {
                                min: 360,
                                ideal: 480,
                                //max: 1080
                            }
                        }
                    }

                    navigator.mediaDevices.getUserMedia(constraints).then(stream => {
                    
                    const videoPlayer = document.querySelector('video');
                    videoPlayer.srcObject = stream;
                    videoPlayer.play;
                });
            }
            else {
                this.$Toast.fire({
                    icon: 'warning',
                    title: 'No Media Devices'
                });
            }
        },
        takePicture(){
            let ratio = (window.innerHeight < window.innerWidth) ? 16/9 : 9/16;
            const picture = document.getElementById('canvas');
            picture.width = (window.innerWidth < 1200) ? window.innerWidth : 1280;
            picture.height = window.innerWidth / ratio;
            const ctx = picture.getContext('2d');
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(document.getElementById('feed'), 0, 0, picture.width, picture.height);
            this.picture = picture.toDataURL();
            this.picTaken = true;
        },
        startExam(){
            this.submitInfo = true;
            this.$axios.post('/api/confirm-password', {
                form: this.form,
                id: this.$route.params.set_id
            })
            .then(({data}) => {
                if(data !== 'wrong'){
                    this.max = data.section.length * 100;

                    for(let x = 0; x < data.section.length; x++){
                        for(let y = 0; y < data.section[x].question.length; y++){
                            this.totalAssets++;
                            
                            if(data.section[x].question[y].audio && !this.iOS){
                                this.totalAssets++;
                            }

                            for(let z = 0; z < data.section[x].question[y].choice_set.length; z++){

                                if(data.section[x].question[y].choice_type === '1'){
                                    for(let i = 0; i < data.section[x].question[y].choice_set[z].choices.length; i++){
                                        this.totalAssets++;
                                    }
                                }
                            }

                        }
                    }

                    for(let x = 0; x < data.section.length; x++){

                        this.progress.push({bar: 0, max: data.section[x].question.length});
                        data.section[x].question = this.shuffle(data.section[x].question);

                        for(let y = 0; y < data.section[x].question.length; y++){

                            const preload_image = new Image();

                            preload_image.src = this.$URL+'/img/question/'+data.section[x].question[y].picture;

                            preload_image.onload = () => {
                                this.assetsLoaded++;
                            }

                            if(data.section[x].question[y].audio){
                                const preload_audio = new Audio();
                                preload_audio.src = this.$URL+'/audio/'+data.section[x].question[y].audio;
                                data.section[x].question[y].audio_counter = 2;
                                
                                if(!this.iOS){
                                    preload_audio.addEventListener('canplaythrough', () => {
                                        this.assetsLoaded++;
                                    })
                                }
                            }

                            for(let z = 0; z < data.section[x].question[y].choice_set.length; z++){

                                if(data.section[x].question[y].choice_type === '1'){
                                    for(let i = 0; i < data.section[x].question[y].choice_set[z].choices.length; i++){
                                        const preload_choice = new Image();
                                        preload_choice.src = this.$URL+'/img/choices/'+data.section[x].question[y].choice_set[z].choices[i].choices;
                                        
                                        preload_choice.onload = () => {
                                            this.assetsLoaded++;
                                        }
                                    }
                                }

                                data.section[x].question[y].choice_set[z].picked = null;
                                data.section[x].question[y].choice_set[z].choices = this.shuffle(data.section[x].question[y].choice_set[z].choices);
                            }
                        }
                    }

                    this.$refs['password-modal'].hide();
                    this.$refs['loading-modal'].show();
                    this.exam = data;
                }else{
                    this.$Toast.fire({
                        icon: 'warning',
                        title: 'Wrong Password, Try Again'
                    });
                    this.form.password = '';
                    this.initExam();
                    this.submitInfo = false;
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
                    {title: 'Time Spent', dataKey: 'TimeSpent'},
                    {title: 'Score', dataKey: 'Score'},
                    {title: 'Passing Score', dataKey: 'Passing'},
                    {title: 'Status', dataKey: 'Status'},
                ];

                let rows = [];
                let timeSpentEach = [];
                let timeSpentAll = 0;

                for(let x = 0; x < this.timeSpent.length; x++){
                    let h = Math.floor(this.timeSpent[x] / 3600);
                    let m = Math.floor((this.timeSpent[x] - h*3600) / 60);
                    let s = Math.floor((this.timeSpent[x]) - (h*3600) - (m*60));
                    timeSpentEach[x] = h + ':' + ((m.toString().length == 1) ? '0'+m : m) + ':' + (s.toString().length == 1 ? '0'+s : s);
                    timeSpentAll += this.timeSpent[x];
                }

                let h = Math.floor(timeSpentAll / 3600);
                let m = Math.floor((timeSpentAll - h*3600) / 60);
                let s = Math.floor(timeSpentAll - (h*3600) - (m*60));

                timeSpentAll = h + ':' + ((m.toString().length == 1) ? '0'+m : m) + ':' + (s.toString().length == 1 ? '0'+s : s);

                timeSpentEach.push(timeSpentAll);

                for(let x = 0; x < data.scores.length; x++){
                    rows.push([]);
                    rows[x].push(data.scores[x].section)
                    rows[x].push(timeSpentEach[x])
                    rows[x].push(data.scores[x].score)
                    rows[x].push(data.scores[x].passing)
                    rows[x].push(data.scores[x].status)
                }
                
                let doc = new jsPDF('p', 'pt');

                doc.text('Set Name: ' + data.set_name, 40, 30)
                doc.text('Student Name: ' + data.stud_name, 40, 50)
                doc.text(40, 70, 'Sensei: ' + data.stud_sensei)

                if(this.picTaken){
                    doc.addImage(this.picture, 'JPEG', 400, 15, 150, 85);
                }
                
                let d = new Date();
                let date = d.getMonth() + '/' + d.getDay() + '/' + d.getFullYear();
                let minutes = d.getMinutes();
                if(minutes.toString().length == 1){
                    minutes = '0' + minutes;
                }

                let time = d.getHours() + ':' + minutes;

                doc.text('Date & Time: ' + date + ' ' + time, 40, 90)

                doc.autoTable({
                    head: [columns],
                    body: rows,
                    margin: {top:120}
                })
                
                doc.save(data.set_name + ' - ' + data.stud_name + ' Result.pdf');

                setTimeout(() => {
                    this.$router.push('/');
                }, 5000)
            })
            .catch(() => {
                this.$Swal.fire({
                    icon: 'warning',
                    title: 'No Internet Connection!',
                    text: 'Please check connection and try again',
                    allowOutsideClick: false,
                    confirmButtonText: 'Submit again'
                }).then((result) => {
                    if(result.value) {
                        this.submitExam();
                    }
                });
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
    watch: {
        assetsLoaded: function(val){
            if(this.totalAssets == val){
                this.allLoaded = true;
            }
        },
        allLoaded: function(val){
            if(val == true){
                setTimeout(() => {
                    this.$refs['loading-modal'].hide();
                    
                    this.$Swal.fire({
                        icon: 'success',
                        title: 'Great!',
                        text: 'Exam will now start',
                        showConfirmButton: false,
                        timer: 2500,
                        onClose: () => {
                            this.examStart = true;
                            
                            setInterval(() => {
                                this.timeSpent[this.current_section]++;
                            }, 1000)
                        }
                    })
                }, 1500);
            }
        },
    },
    created() {
    },
    mounted() {
        this.initExam();
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
    .tempImage{
        display: inline-block;
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        height: 500px;
    }
    .camera {
        width: 80%;
        max-height: 500px;
        margin: 0 auto;
    }
    #feed, .picture{
        display: block;
        width: 80%;
        max-height: 300px;
        margin: 0 auto;
        background-color: #171717;
        box-shadow: 5px 5px 12px 0px rgba(0,0,0, 0.25);
    }
    #canvas{
        display: block;
        width: 100%;
        max-height: 300px;
        margin: 0 auto;
    }
</style>
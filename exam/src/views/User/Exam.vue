<template>
    <div class="card mt-5 justify-content-center">
        <div class="card-body">
            <div class="row">
                <div v-if="examStart" class="col-md-3">
                    <div class="sticky-div">
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

                                <button 
                                    :disabled="playing || exam.section[current_section].question[current].audio_counter < 1" 
                                    @click="playAudio(exam.section[current_section].question[current].audio)" class="btn btn-md btn-success"><i class="fa fa-play"></i></button>&nbsp;
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
                </div>

                <div v-if="examStart" class="col-md-9">

                    <div class="col-md-12">
                        <img :src="$URL+'/img/question/'+exam.section[current_section].question[current].picture" alt="question" class="w-100 border">
                    </div>
                    <div class="col-md-12 overflow-auto mt-3" style="font-size: 24px; min-height: 100px; max-height: 400px;">
                        <div v-for="(choice_set, index) in exam.section[current_section].question[current].choice_set" :key="index" class="row border border-dark mb-2 pb-2">
                            <div class="col-md-12 font-weight-bold">
                                {{ choice_set.description }}
                            </div>
                            <div v-if="exam.section[current_section].question[current].choice_type == true" class="col-md-12">
                                <div class="row">
                                    <div v-for="(choices, index2) in choice_set.choices" :key="index2" class="col-6 col-sm-6 col-md-3 p-1">
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

        <b-modal centered no-close-on-backdrop no-close-on-esc hide-footer
            id="password-modal"
            ref="password-modal"
            title="Enter Info"
            ok-title="Submit"
            @ok="startExam">
            <b-form @submit.prevent="startExam">

                <b-form-group v-if="mediaAvailable">
                    <div class="camera" v-show="!picTaken">
                        <video autoplay id="feed"></video>
                    </div>
                    
                    <div class="picture" v-show="picTaken">
                        <canvas id="canvas"></canvas>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-success mt-1" @click="takePicture" :disabled="continueExam">Take Photo</button>
                    </div>
                    
                </b-form-group>

                <b-form-group v-else>
                    <div class="text-center">
                        <h3><b-badge variant="danger">No Cameras Detected</b-badge></h3>
                    </div>
                </b-form-group>

                <b-form-group>
                    <b-form-input
                        autocomplete="off" 
                        type="text"
                        required
                        placeholder="Enter Your Name"
                        v-model="form.name"
                        :disabled="continueExam"
                    >
                    </b-form-input>
                </b-form-group>

                <b-form-group>
                    <b-form-input
                        autocomplete="off" 
                        placeholder="Enter Your Teacher's Name"
                        required
                        v-model="form.sensei"
                        :disabled="continueExam"
                    >
                    </b-form-input>
                </b-form-group>

                <b-form-group>
                    <b-form-datepicker
                        v-model="form.start"
                        :disabled="continueExam"
                        required
                        placeholder="Enter Start of Class"
                    >

                    </b-form-datepicker>
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
var audio, interval

export default {
    name: 'jlt_exam',
    data() {
        return {
            form: {
                name: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('name')) : '',
                sensei: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('sensei')) : '',
                start: (localStorage.getItem('start')) ? JSON.parse(localStorage.getItem('start')) : '',
                password: ''
            },
            variant: ['info', 'danger', 'primary', 'success'],
            max: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('max')) : 0,
            progress: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('progress')) : [],
            playing: false,
            submit: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('submit')) : false,
            submitDisabled: false,
            current_section: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('current_section')) : 0,
            current: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('current')) : 0,
            totalAssets: 0,
            assetsLoaded: 0,
            allLoaded: false,
            examStart: false,
            exam: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('exam')) : null,
            submitInfo: false,
            timeSpent: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('timeSpent')) : [{'time': 0}, {'time': 0}, {'time': 0}, {'time': 0},],
            iOS: isIOS ? true : false,
            picture: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('picture')) : null,
            picTaken: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('picTaken')) : false,
            mediaAvailable: ('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices) ? true : false,
            continueExam: false,
        }
    },
    methods: {
        resetData(){
            this.form.name = '';
            this.form.sensei = '';
            this.max = 0;
            this.progress = [];
            this.submit = false;
            this.current_section = 0;
            this.current = 0;
            this.exam = null;
            this.timeSpent = [{'time': 0}, {'time': 0}, {'time': 0}, {'time': 0},];
            this.picture = null;
            this.picTaken = false;
        },
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
                        this.mediaAvailable = true;
                        const videoPlayer = document.querySelector('video');
                        videoPlayer.srcObject = stream;
                        videoPlayer.play;
                    }).catch(() => {
                        this.mediaAvailable = false;
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
                this.form.password = '';
                if(data !== 'wrong'){
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

                    if(this.continueExam){
                        for(let x = 0; x < this.exam.section.length; x++){
                            for(let y = 0; y < this.exam.section[x].question.length; y++){
                                const preload_image = new Image();

                                preload_image.src = this.$URL+'/img/question/'+data.section[x].question[y].picture;

                                preload_image.onload = () => {
                                    this.assetsLoaded++;
                                }

                                if(this.exam.section[x].question[y].audio){
                                    const preload_audio = new Audio();
                                    preload_audio.src = this.$URL+'/audio/'+this.exam.section[x].question[y].audio;
                                    
                                    if(!this.iOS){
                                        preload_audio.addEventListener('canplaythrough', () => {
                                            this.assetsLoaded++;
                                        })
                                    }
                                }

                                for(let z = 0; z < this.exam.section[x].question[y].choice_set.length; z++){

                                    if(this.exam.section[x].question[y].choice_type === '1'){
                                        for(let i = 0; i < this.exam.section[x].question[y].choice_set[z].choices.length; i++){
                                            const preload_choice = new Image();
                                            preload_choice.src = this.$URL+'/img/choices/'+this.exam.section[x].question[y].choice_set[z].choices[i].choices;
                                            
                                            preload_choice.onload = () => {
                                                this.assetsLoaded++;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else{
                        this.max = data.section.length * 100;

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

                            this.exam = data;
                        }

                    }

                    this.$refs['password-modal'].hide();
                    this.$refs['loading-modal'].show();
                }else{
                    this.$Toast.fire({
                        icon: 'warning',
                        title: 'Wrong Password, Try Again'
                    });
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
                confirmButtonText: 'Proceed',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.value) {
                    this.resetAudio();
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
                    let h = Math.floor(this.timeSpent[x].time / 3600);
                    let m = Math.floor((this.timeSpent[x].time - h*3600) / 60);
                    let s = Math.floor((this.timeSpent[x].time) - (h*3600) - (m*60));
                    timeSpentEach[x] = h + ':' + ((m.toString().length == 1) ? '0'+m : m) + ':' + (s.toString().length == 1 ? '0'+s : s);
                    timeSpentAll += this.timeSpent[x].time;
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
                let date = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate();
                let minutes = d.getMinutes();
                if(minutes.toString().length == 1){
                    minutes = '0' + minutes;
                }

                let time = d.getHours() + ':' + minutes;

                doc.text('Start of Class: ' + this.form.start, 40, 90)
                doc.text('Date & Time: ' + date + ' ' + time, 40, 110)

                doc.autoTable({
                    head: [columns],
                    body: rows,
                    margin: {top:130}
                })
                
                doc.save(data.set_name + ' - ' + data.stud_name + ' Result.pdf');

                setTimeout(() => {
                    clearInterval(interval);
                    localStorage.clear();
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
        },
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
                            
                            interval = setInterval(() => {
                                this.timeSpent[this.current_section].time++;
                                this.exam.time--;
                            }, 1000)
                        }
                    })
                }, 1500);
            }
        },
        'form.name': {
            immediate: true,
            handler(data) {
                localStorage.setItem('name', JSON.stringify(data));
            },
            deep: true
        },
        'form.sensei': {
            immediate: true,
            handler(data) {
                localStorage.setItem('sensei', JSON.stringify(data));
            },
            deep: true
        },
        'form.start': {
            immediate: true,
            handler(data) {
                localStorage.setItem('start', JSON.stringify(data));
            },
            deep: true
        },
        max: {
            immediate: true,
            handler(data) {
                localStorage.setItem('max', JSON.stringify(data))
            },
            deep: true
        },
        progress: {
            immediate: true,
            handler(data) {
                localStorage.setItem('progress', JSON.stringify(data))
            },
            deep: true
        },
        submit: {
            immediate: true,
            handler(data) {
                localStorage.setItem('submit', JSON.stringify(data));
            },
            deep: true
        },
        current_section: {
            immediate: true,
            handler(data) {
                localStorage.setItem('current_section', JSON.stringify(data));
            },
            deep: true
        },
        current: {
            immediate: true,
            handler(data) {
                localStorage.setItem('current', JSON.stringify(data));
            },
            deep: true
        },
        picture: {
            immediate: true,
            handler(data) {
                localStorage.setItem('picture', JSON.stringify(data));
            },
            deep: true
        },
        picTaken: {
            immediate: true,
            handler(data) {
                localStorage.setItem('picTaken', JSON.stringify(data));
            },
            deep: true
        },
        timeSpent: {
            immediate: true,
            handler(data) {
                localStorage.setItem('timeSpent', JSON.stringify(data));
            },
            deep: true
        },
        exam: {
            immediate: true,
            handler(data) {
                localStorage.setItem('exam', JSON.stringify(data));
            },
            deep: true
        },

    },
    created() {
    },
    mounted() {
        if(this.exam){
            this.$Swal.fire({
                title: 'Ongoing Exam',
                text: "An exam is currently active, would you like to continue?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Proceed',
                allowOutsideClick: false,
            }).then((result) => {
                if (result.value) {
                    this.continueExam = true;
                    this.initExam();
                }
                else{
                    localStorage.clear();
                    this.resetData();
                    this.initExam();
                }
            })
        }
        else{
            this.initExam();
        }
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
    .sticky-div{
        position: -webkit-sticky; /* Safari & IE */
        position: sticky;
        top: 20px;
    }
</style>
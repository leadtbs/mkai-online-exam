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
                                    @finish="timesUp"
                                ></circular-count-down-timer>
                            </div>
                        </div>

                        <div class="row mt-3 text-center" v-if="examStart">
                            <div class="col-md-12 font-weight-bold text-success">
                                Progress
                            </div>
                            <div class="col-md-12">
                                <b-progress striped :max="max" :value="bar" variant="primary"></b-progress>
                            </div>
                        </div>

                        <div class="row mt-4 mb-2" v-if="examStart">
                            <div class="col-md-12 text-center m-auto" >
                                <button
                                    v-for="(question, index) in exam.question"
                                    :key="index"
                                    :class="[dottedProgress(index), dottedPicked(index)]"
                                    @click="pickQuestion(index)"
                                    class="rounded-circle btn btn-sm ml-1 mr-1 mt-2 font-weight-bold text-white circle-pick"
                                    style="width: 35px;">{{ index+1 }}</button>
                            </div>
                        </div>

                        <div class="row mt-4 mb-2">
                            <div class="col-md-12 text-center">
                                <button :disabled="submitDisabled" @click="submitButton()" class="btn btn-md btn-danger">Submit</button>&nbsp;
                                <button :disabled="submitDisabled" @click="nextQuestion()" class="btn btn-md btn-success">Next Question</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="examStart" class="col-md-9">
                    <div class="col-md-12">
                        <img :src="$URL+'/img/ncquestion/'+exam.question[current].picture" alt="question" class="w-100 border">
                    </div>
                    <div class="col-md-12 overflow-auto mt-3" style="font-size: 24px;">
                        <div v-for="(choice_set, index) in exam.question[current].choice_set" :key="index" class="row border border-dark mb-2 pb-2">
                            <div class="col-md-12 font-weight-bold">
                                {{ choice_set.description }}
                            </div>
                            <div v-if="exam.question[current].choice_type == true" class="col-md-12">
                                <div class="row">
                                    <div v-for="(choices, index2) in choice_set.choices" :key="index2" class="col-6 col-sm-6 col-md-3 p-1">
                                        <div @click="pick(index, index2)" v-bind:value="index2" class="border mt-3 text-wrap clickable" v-if="choices.choices != ''">
                                            <img :src="$URL+'/img/ncchoices/'+choices.choices" alt="choice" class="w-100 h-100" :class="ifPicked(index, index2)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="exam.question[current].choice_type == false" class="col-md-12">
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
import jsPDF from 'jspdf'
import 'jspdf-autotable'
var interval

export default {
    name: 'nc_exam',
    data() {
        return {
            // BOTH -- START
            form: {
                name: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('name')) : null, // ''
                sensei: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('sensei')) : null, // ''
                start: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('start')) : null, // ''
                password: ''
            },
            max: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('max')) : null, // 0
            bar: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('bar')) : null, // 0
            set_type: this.$route.params.set_type, // set_type params
            submit: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('submit')) : null, // false
            submitDisabled: false,
            totalAssets: 0,
            assetsLoaded: 0,
            allLoaded: false,
            examStart: false,
            submitInfo: false,
            picture: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('picture')) : '', // null
            picTaken: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('picTaken')) : null, // false
            mediaAvailable: ('mediaDevices' in navigator && 'getUserMedia' in navigator.mediaDevices) ? true : false,
            continueExam: false,
            exam: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('exam')) : null,
            current: (localStorage.getItem('exam')) ? JSON.parse(localStorage.getItem('current')) : null, // 0
        }
    },
    methods: {
        resetData(){
            this.form.name = '';
            this.form.sensei = '';
            this.form.start = '';
            this.max = 0;
            this.submit = false;
            this.current = 0;
            this.exam = null;
            this.picture = null;
            this.picTaken = false;
            this.set_type = this.$route.params.set_type;
        },
        shuffleQ(array){
            let finalArray = [];
            let tempArray = [];
            let tempCat = 0;
            for(let x = 0; x < array.length; x++){
                if(array[x].category_id != tempCat){
                    tempArray = [];
                    tempCat = array[x].category_id;

                    tempArray.push(array[x]);
                }
                else if(array[x].category_id == tempCat){
                    tempArray.push(array[x]);
                }

                if(typeof array[x+1] !== 'undefined'){
                    if(array[x+1].category_id != tempCat){
                        tempArray = this.shuffle(tempArray);
                        for(let y = 0; y < tempArray.length; y++){
                            finalArray.push(tempArray[y]);
                        }
                    }
                }
                else if(typeof array[x+1] === 'undefined'){
                    tempArray = this.shuffle(tempArray);
                    for(let y = 0; y < tempArray.length; y++){
                        finalArray.push(tempArray[y]);
                    }
                }
            }

            return finalArray;
        },
        shuffle(array){
            for(let i = array.length - 1; i > 0; i--){
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
            localStorage.setItem('set_type', JSON.stringify(this.$route.params.set_type));
            let d = new Date();
            let date = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate();
            let minutes = d.getMinutes();
            if(minutes.toString().length == 1){
                minutes = '0' + minutes;
            }
            let time = d.getHours() + ':' + minutes;
            
            this.submitInfo = true;
            this.$axios.post('/api/confirm-password', {
                date: date + ' ' + time,
                form: this.form,
                id: this.$route.params.set_id,
                set_type: this.set_type,
                continue: this.continueExam
            })
            .then(({data}) => {
                this.form.password = '';
                if(data !== 'wrong'){
                    if(this.mediaAvailable){
                        const video = document.querySelector('video');
                        const mediaStream = video.srcObject;
                        if(mediaStream != null){
                            const tracks = mediaStream.getTracks();
                            tracks.forEach(track => track.stop())
                        }
                    }

                    for(let x = 0; x < data.question.length; x++){
                        this.totalAssets++;

                        for(let y = 0; y < data.question[x].choice_set.length; y++){
                            if(data.question[x].choice_type === '1'){
                                for(let z = 0; z < data.question[x].choice_set[z].choices.length; z++){
                                    this.totalAssets++;
                                }
                            }
                        }
                    }
                    
                    if(this.continueExam){
                        for(let x = 0; x < data.question.length; x++){
                            const preload_image = new Image();

                            preload_image.src = this.$URL+'/img/ncquestion/'+data.question[x].picture;

                            preload_image.onload = () => {
                                this.assetsLoaded++;
                            }

                            for(let y = 0; y < data.question[x].choice_set.length; y++){

                                if(data.question[x].choice_type === '1'){
                                    for(let z = 0; z < data.question[x].choice_set[y].choices.length; z++){
                                        const preload_choice = new Image();
                                        preload_choice.src = this.$URL+'/img/ncchoices/'+data.question[x].choice_set[y].choices[z].choices;
                                        
                                        preload_choice.onload = () => {
                                            this.assetsLoaded++;
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        
                        this.max = data.question.length;
                        data.question = this.shuffleQ(data.question);

                        for(let x = 0; x < data.question.length; x++){

                            const preload_image = new Image();

                            preload_image.src = this.$URL+'/img/ncquestion/'+data.question[x].picture;

                            preload_image.onload = () => {
                                this.assetsLoaded++;
                            }

                            for(let y = 0; y < data.question[x].choice_set.length; y++){

                                if(data.question[x].choice_type === '1'){
                                    for(let z = 0; z < data.question[x].choice_set[y].choices.length; z++){
                                        const preload_choice = new Image();
                                        preload_choice.src = this.$URL+'/img/ncchoices/'+data.question[x].choice_set[y].choices[z].choices;
                                        
                                        preload_choice.onload = () => {
                                            this.assetsLoaded++;
                                        }
                                    }
                                }

                                data.question[x].choice_set[y].picked = null;
                                data.question[x].choice_set[y].choices = this.shuffle(data.question[x].choice_set[y].choices);
                            }
                        }

                        this.exam = data;
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
        pick(choice_set_index, choice_index){
            this.exam.question[this.current].choice_set[choice_set_index].picked = choice_index;
        },
        ifPicked(index, index2){
            return (index2 == this.exam.question[this.current].choice_set[index].picked) ? 'picked' : '';
        },
        dottedProgress(index){
            let count = 0;
            let picked = true;
            let question = this.exam.question;
            let choice_set = this.exam.question[index].choice_set;

            for(let x = 0; x < question.length; x++){
                let counter = true;
                for(let y = 0; y < question[x].choice_set.length; y++){
                    if(question[x].choice_set[y].picked == null){
                        counter = false;
                        break;
                    }
                }
                if(counter == true){
                    count++;
                }
            }

            this.bar = count;

            for(let x = 0; x < choice_set.length; x++){
                if(choice_set[0].picked == null){
                    picked = false;
                    break;
                }
            }

            return (picked) ? 'bg-answered' : 'bg-unanswered';
        },
        dottedPicked(index){
            return (this.current == index) ? 'btn-outline-danger' : '';
        },
        pickQuestion(index){
            this.current = index;
        },
        nextQuestion(){
            if(this.current < this.exam.question.length-1){
                this.$Progress.start();
                this.current++;
                this.$Progress.finish();
            }else{
                this.$Progress.start();
                this.current = 0;
                this.$Progress.finish();
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
        timesUp(){
            if(this.submitDisabled == false){
                this.$Swal.fire({
                    icon: 'warning',
                    title: `Time's Up!`,
                    text: 'A PDF File with your result will be downloaded shortly',
                    showConfirmButton: false,
                    timer: 5000,
                })
                this.submitExam();
            }
        },
        submitExam(){
            let d = new Date();
            let date = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + d.getDate();
            let minutes = d.getMinutes();
            if(minutes.toString().length == 1){
                minutes = '0' + minutes;
            }
            let time = d.getHours() + ':' + minutes;

            this.submitDisabled = true;
            this.$axios.post('/api/submit_nc_exam', {
                date: date + ' ' + time,
                set_type: this.set_type,
                exam: this.exam
            })
            .then(({data}) => {
                let columns = [
                    {title: 'Category', dataKey: 'Category'},
                    {title: 'Score', dataKey: 'Score'},
                    {title: 'Passing Score', dataKey: 'Passing Score'},
                    {title: 'Status', dataKey: 'Status'},
                ]

                let rows = [];
                
                for(let x = 0; x < data.scores.length; x++){
                    rows.push([]);
                    rows[x].push(data.scores[x].category)
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

                doc.text('Start of Class: ' + this.form.start, 40, 90)
                doc.text('Date & Time: ' + date + ' ' + time, 40, 110)

                doc.autoTable({
                    head: [columns],
                    body: rows,
                    margin: {top:130}
                })
                
                let pdf = btoa(doc.output());

                this.$axios.post('/api/download_result', {
                    pdf: pdf,
                    type: this.set_type,
                    set: data.set_name,
                    student: data.stud_name,
                    date: date,
                    time: time,
                });
                
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

                            let temp = JSON.parse(localStorage.getItem('exam'));
                            if(temp.time < 1){
                                this.timesUp();
                            }
                            
                            interval = setInterval(() => {
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
        max: {
            immediate: true,
            handler(data) {
                localStorage.setItem('max', JSON.stringify(data))
            },
            deep: true
        },
        bar: {
            immediate: true,
            handler(data) {
                localStorage.setItem('bar', JSON.stringify(data))
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
        current: {
            immediate: true,
            handler(data) {
                localStorage.setItem('current', JSON.stringify(data));
            },
        },
        exam: {
            immediate: true,
            handler(data) {
                localStorage.setItem('exam', JSON.stringify(data));
            },
            deep: true
        },
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
            localStorage.clear();
            this.resetData();
            this.initExam();
        }
    }
}
</script>
<template>
    <div>
        <div class="card-header font-weight-bold">
            Exam Set List
            <b-button @click="clearForm(); editState=false; $refs['add-set-modal'].show();" variant="success" size="sm" class="float-right">Add Set <font-awesome-icon class="fa-fw" icon="plus-square" /></b-button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mt-2 p-0" v-for="set in setList" :key="set.id">
                    <table class="text-center table-bordered m-auto" style="width: 95%;">
                        <tr>
                            <td @click="goToQuestions(set.id)" style="height: 50px;" colspan="5" class="align-middle font-weight-bold set-pick">{{ set.name }}</td>
                        </tr>
                        <tr>
                            <td class="align-middle" style="width:20%"><font-awesome-icon class="fa-fw text-info" icon="clock" />{{ set.time }}</td>
                            <td class="align-middle" style="width:15%"><font-awesome-icon class="fa-fw text-success" icon="question" />{{ set.question_count }}</td>
                            <td class="align-middle" style="width:15%"><font-awesome-icon class="fa-fw text-warning" icon="star" />{{ set.choice_count }}</td>
                            <td class="align-middle" style="width:10%">
                                <b-button variant="primary" size="xs" @click="editSetForm(set)"><font-awesome-icon icon="edit" /></b-button>
                            </td>
                            <td class="align-middle" style="width:10%">
                                <b-button variant="danger" size="xs" @click="deleteSetForm(set.id)"><font-awesome-icon icon="trash" /></b-button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
        </div>
            
        <b-modal centered
            id="add-set-modal"  
            title="Set"
            ok-title="Save Changes"
            ref="add-set-modal"
            @ok="submitForm">
            <b-form @submit.prevent="submitForm">

                <b-form-group>
                    <b-form-input
                    type="text"
                    required
                    placeholder="Set Name"
                    v-model="form.name"
                    ></b-form-input>
                </b-form-group>

                <b-form-group>
                    <TimePicker 
                        autocomplete="off" 
                        placeholder="Duration HH:MM"
                        name="time"
                        v-model="form.time"
                        :config="options">
                    </TimePicker>
                </b-form-group>

                <b-form-group>
                    <b-form-input
                    type="password"
                    required
                    placeholder="Set Password"
                    v-model="form.password"
                    ></b-form-input>
                </b-form-group>

                <b-form-group class="d-none">
                    <b-button type="submit">Save Changes</b-button>
                </b-form-group>

            </b-form>
        </b-modal>
    </div>
</template>

<script>
import TimePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import jQuery from 'jquery'
import { Form } from 'vform'

jQuery.extend(true, jQuery.fn.datetimepicker.defaults, {
    icons: {
        time: 'fas fa-clock',
        up: 'fas fa-arrow-up',
        down: 'fas fa-arrow-down',
        clear: 'fa fa-trash-alt',
        close: 'fa fa-times-circle'
    }
});

export default {
    data(){
        return {
            editState: false,
            setList: {},
            options: {
                format: 'H:mm',
                useCurrent: false,
                showClear: true,
                showClose: true
            },
            form: new Form({
                id: '',
                name: '',
                time: '',
                password: ''
            }),
            set_type: '1'
        }
    },
    methods: {
        goToQuestions(id){
            this.$router.push({ name: 'question', params: { set_id: id }})
        },
        editSetForm(set){
            this.clearForm();
            this.editState = true;
            this.form.fill(set);
            this.$refs['add-set-modal'].show();
        },
        submitForm(){
            this.$Progress.start();
            if(this.form.password == ''){
                this.form.password = undefined;
            }
            if(this.editState == false){
                this.$axios.post('api/save_set', this.form, {
                    params: {
                        set_type: this.set_type
                    }
                })
                .then(() => {
                    this.$refs['add-set-modal'].hide();
                    this.loadSets();
                    this.$Progress.finish();
                    this.$Toast.fire({
                        icon: 'success',
                        title: 'Data added successfully'
                    });
                })
                .catch(() => {
                    this.$Progress.fail();
                })
            }
            else{
                this.form.put('api/save_set/'+this.form.id)
                .then(() => {
                    this.$refs['add-set-modal'].hide();
                    this.loadSets();
                    this.$Progress.finish();
                    this.$Toast.fire({
                        icon: 'success',
                        title: 'Data added successfully'
                    });
                })
                .catch(() => {
                    this.$Progress.fail();
                })
            }
        },
        deleteSetForm(set_id){
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
                    this.$axios.delete('api/save_set/'+set_id)
                    .then(() => {
                        this.loadSets();
                        this.$Progress.finish();
                        this.$Toast.fire({
                            icon: 'success',
                            title: 'Data deleted successfully'
                        });
                    })
                    .catch(() => {
                        this.$Swal.fire('Failed!', 'There was something wrong', 'warning');
                    })
                }
            })
        },
        loadSets(){
            this.$Progress.start();
            this.$axios.get('api/set', {
                params: {
                    set_type: this.set_type
                }
            })
            .then(({data}) => {
                for(let x = 0; x < data.length; x++){
                    let hours = Math.floor((data[x].time / (60 * 60)) % 24);
                    let minutes = Math.floor((data[x].time / 60) % 60);

                    data[x].time = hours + ':' + ((minutes < 10) ? '0' + minutes : minutes);
                }
                this.setList = data;
                this.$Progress.finish();
            })
        },
        clearForm(){
            this.form.id = '';
            this.form.name = '';
            this.form.time = '';
            this.form.password = '';
        }
    },
    components: {
        TimePicker
    },
    created() {
        switch(this.$route.params.set_type){
            case 'jlt': this.set_type = 1; break;
            case 'nce': this.set_type = 2; break;
            case 'ncj': this.set_type = 3; break;
            default: console.log('mao ni');
        }
        this.loadSets();
    }
}
</script>